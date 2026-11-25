<?php
/**
 * Envoi du formulaire de contact – hébergement Infomaniak (PHP).
 *
 * - Destinataire fixe (impossible de s'en servir pour écrire à quelqu'un d'autre).
 * - Protections anti-spam : champ piège, envoi trop rapide, limite par adresse IP.
 * - Aucun saut de ligne accepté dans les en-têtes (pas d'injection d'en-têtes).
 * - Répond en JSON : {"ok": true} ou {"ok": false, "error": "..."}.
 */
declare(strict_types=1);

const TO        = 'contact@canifelinharmonie.ch';
const FROM      = 'contact@canifelinharmonie.ch';
const SITE      = 'canifelinharmonie.ch';
const MAX_PER_H = 6; // demandes maximum par heure et par adresse IP
const SMTP_HOST_DEFAULT = 'mail.infomaniak.com';
const SMTP_PORT_DEFAULT = 587;

// Ne jamais afficher d'erreur PHP au visiteur (elle révélerait des chemins du serveur)
ini_set('display_errors', '0');
date_default_timezone_set('Europe/Zurich');
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

/**
 * Lit une variable de configuration depuis l'environnement serveur (SetEnv / getenv)
 * ou depuis un fichier .env non versionné (au-dessus ou à côté du script, bloqué par .htaccess).
 */
function env_val(string $key, string $default = ''): string
{
    $v = getenv($key);
    if (is_string($v) && $v !== '') {
        return $v;
    }
    if (!empty($_ENV[$key])) {
        return (string)$_ENV[$key];
    }
    if (!empty($_SERVER[$key])) {
        return (string)$_SERVER[$key];
    }
    static $dotenv = null;
    if ($dotenv === null) {
        $dotenv = [];
        foreach ([dirname(__DIR__) . '/.env', __DIR__ . '/.env'] as $path) {
            if (is_readable($path)) {
                foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                        continue;
                    }
                    [$k, $val] = explode('=', $line, 2);
                    $k = trim($k);
                    $val = trim($val);
                    if (
                        (str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                        (str_starts_with($val, "'") && str_ends_with($val, "'"))
                    ) {
                        $val = substr($val, 1, -1);
                    }
                    $dotenv[$k] = $val;
                }
                break;
            }
        }
    }
    return $dotenv[$key] ?? $default;
}

/**
 * Envoi SMTP authentifié via STARTTLS (port 587, mail.infomaniak.com).
 */
function send_smtp(string $to, string $replyTo, string $subject, string $body, string $html = ''): bool
{
    $host = env_val('SMTP_HOST', SMTP_HOST_DEFAULT);
    $port = (int)env_val('SMTP_PORT', (string)SMTP_PORT_DEFAULT);
    $user = env_val('SMTP_USER', FROM);
    $pass = env_val('SMTP_PASS', '');

    if ($pass === '') {
        return false;
    }

    $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 10);
    if (!$fp) {
        return false;
    }
    stream_set_timeout($fp, 10);

    $readCode = static function () use ($fp): int {
        $code = 0;
        while (($line = fgets($fp, 515)) !== false) {
            $code = (int)substr($line, 0, 3);
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $code;
    };

    $cmd = static function (string $c, int $expect) use ($fp, $readCode): bool {
        if (@fwrite($fp, $c . "\r\n") === false) {
            return false;
        }
        return $readCode() === $expect;
    };

    if ($readCode() !== 220) {
        fclose($fp);
        return false;
    }
    if (!$cmd('EHLO ' . SITE, 250)) {
        fclose($fp);
        return false;
    }
    if (!$cmd('STARTTLS', 220)) {
        fclose($fp);
        return false;
    }
    $crypto = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
    if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
        $crypto |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
    }
    if (!@stream_socket_enable_crypto($fp, true, $crypto)) {
        fclose($fp);
        return false;
    }
    if (!$cmd('EHLO ' . SITE, 250) || !$cmd('AUTH LOGIN', 334)) {
        fclose($fp);
        return false;
    }
    if (!$cmd(base64_encode($user), 334) || !$cmd(base64_encode($pass), 235)) {
        fclose($fp);
        return false;
    }
    if (!$cmd('MAIL FROM:<' . FROM . '>', 250) || !$cmd('RCPT TO:<' . $to . '>', 250) || !$cmd('DATA', 354)) {
        fclose($fp);
        return false;
    }

    // Points en début de ligne doublés, et fins de ligne CRLF obligatoires (pas de « LF » seul,
    // refusé par les serveurs mail récents)
    $text = str_replace("\n", "\r\n", preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $body)) ?? $body);

    $head = [
        'Date: ' . date('r'),
        'From: ' . mb_encode_mimeheader('Site Canifelin Harmonie', 'UTF-8') . ' <' . FROM . '>',
        'To: <' . $to . '>',
        'Reply-To: ' . $replyTo,
        'Subject: ' . mb_encode_mimeheader($subject, 'UTF-8'),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . SITE . '>',
        'MIME-Version: 1.0',
        'X-Mailer: ' . SITE,
    ];
    if ($html === '') {
        $parts = array_merge($head, [
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $text,
        ]);
    } else {
        // Version HTML (avec logo) + version texte pour les messageries qui n'affichent pas le HTML.
        // Le HTML est encodé en base64 : aucune ligne trop longue, aucun point en début de ligne.
        $b = 'chf-' . bin2hex(random_bytes(10));
        $parts = array_merge($head, [
            'Content-Type: multipart/alternative; boundary="' . $b . '"',
            '',
            '--' . $b,
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $text,
            '--' . $b,
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
            '',
            rtrim(chunk_split(base64_encode($html), 76, "\r\n")),
            '--' . $b . '--',
        ]);
    }
    $msg = implode("\r\n", $parts);

    $ok = $cmd($msg . "\r\n.", 250);
    @fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return $ok;
}

function reply(bool $ok, int $code = 200, string $error = ''): void
{
    http_response_code($code);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Texte sur une ligne : sans caractères de contrôle ni saut de ligne, longueur limitée. */
function line(string $key, int $max = 200): string
{
    $v = (string)($_POST[$key] ?? '');
    $v = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $v) ?? '';
    return mb_substr(trim($v), 0, $max);
}

/** Texte libre (message) : sauts de ligne conservés, caractères de contrôle retirés. */
function text(string $key, int $max = 5000): string
{
    $v = str_replace(["\r\n", "\r"], "\n", (string)($_POST[$key] ?? ''));
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $v) ?? '';
    return mb_substr(trim($v), 0, $max);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    reply(false, 405, 'method');
}

// Le formulaire doit venir du site lui-même
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    $host = parse_url($origin, PHP_URL_HOST) ?: '';
    // « localhost » accepté uniquement pour un test local explicite (jamais en ligne)
    $localTest = getenv('CHF_LOCAL_TEST') === '1' && $host === 'localhost';
    if ($host !== SITE && $host !== 'www.' . SITE && !$localTest) {
        reply(false, 403, 'origin');
    }
}

// Pièges à robots : on répond « ok » sans rien envoyer
if (line('_gotcha') !== '') {
    reply(true);
}
$elapsed = (int)($_POST['_elapsed'] ?? 0);
if ($elapsed > 0 && $elapsed < 3000) {
    reply(true);
}

// Limite d'envois par adresse IP : empreinte pseudonymisée (jamais l'adresse en clair),
// effacée au plus tard une heure après le dernier envoi.
$ip     = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
$dir    = sys_get_temp_dir();
$bucket = $dir . '/chf-' . hash('sha256', $ip . SITE);
$now    = time();
foreach (glob($dir . '/chf-*') ?: [] as $old) {
    if (@filemtime($old) < $now - 3600) {
        @unlink($old);
    }
}
$hits   = [];
if (is_file($bucket)) {
    $hits = array_filter(
        array_map('intval', explode(',', (string)@file_get_contents($bucket))),
        fn(int $t) => $t > $now - 3600
    );
}
if (count($hits) >= MAX_PER_H) {
    reply(false, 429, 'rate');
}

// Champs
$nom         = line('nom', 120);
$email       = line('email', 160);
$telephone   = line('telephone', 40);
$animal      = line('animal', 40);
$prestation  = line('prestation', 120);
$infosAnimal = line('infos_animal', 160);
$source      = line('source', 80);
$message     = text('message', 5000);
$consent     = line('consentement', 10);
$arrivee     = line('arrivee', 200);
$provenance  = line('provenance', 300);
$campagne    = line('campagne', 200);
$page        = line('page', 200);

if ($nom === '' || $animal === '' || $prestation === '' || $message === '' || $consent === '') {
    reply(false, 422, 'required');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    reply(false, 422, 'email');
}

// E-mail reçu par Brigitte
$subject = 'Demande de rendez-vous (site) – ' . $prestation;
$body = implode("\n", [
    'Nouvelle demande envoyée depuis le site ' . SITE,
    '',
    'Nom : ' . $nom,
    'E-mail : ' . $email,
    'Téléphone : ' . ($telephone !== '' ? $telephone : '-'),
    'Animal : ' . $animal,
    'Prestation : ' . $prestation,
    'Nom, âge, race : ' . ($infosAnimal !== '' ? $infosAnimal : '-'),
    '',
    'Message :',
    $message,
    '',
    '— Origine du contact —',
    'Comment m’avez-vous connue : ' . ($source !== '' ? $source : '-'),
    'Arrivée sur le site par la page : ' . ($arrivee !== '' ? $arrivee : '-'),
    'Venu de : ' . ($provenance !== '' ? $provenance : '-'),
    'Campagne : ' . ($campagne !== '' ? $campagne : '-'),
    'Formulaire rempli sur : ' . ($page !== '' ? $page : '-'),
    'Date : ' . date('d.m.Y H:i'),
    '',
    'Pour répondre, utilisez simplement « Répondre » : la réponse ira à ' . $email . '.',
]);

// Même e-mail en HTML : logo, champs en tableau, bouton « Répondre » (styles en ligne, compatibles messageries)
$h = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$or = static fn(string $s): string => $s !== '' ? $s : '-';
$row = static fn(string $label, string $value): string =>
    '<tr><td style="padding:9px 16px 9px 0;color:#566673;font-size:14px;white-space:nowrap;vertical-align:top;border-bottom:1px solid #efe5d8">'
    . $h($label) . '</td><td style="padding:9px 0;color:#1a2a35;font-size:15px;vertical-align:top;border-bottom:1px solid #efe5d8">'
    . $value . '</td></tr>';
$small = static fn(string $label, string $value): string =>
    '<tr><td style="padding:4px 14px 4px 0;color:#566673;font-size:13px;white-space:nowrap;vertical-align:top">' . $h($label)
    . '</td><td style="padding:4px 0;color:#1a2a35;font-size:13px;vertical-align:top;word-break:break-word">' . $h($value) . '</td></tr>';
$replyHref = 'mailto:' . rawurlencode($email) . '?subject=' . rawurlencode('Re: Votre demande – ' . $prestation);
$telLink = $telephone !== ''
    ? '<a href="tel:' . $h(preg_replace('/[^\d+]/', '', $telephone) ?? '') . '" style="color:#2770a6;text-decoration:none">' . $h($telephone) . '</a>'
    : '-';

$html = '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
    . '<title>' . $h($subject) . '</title></head>'
    . '<body style="margin:0;padding:0;background:#fbf5ee;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Helvetica,Arial,sans-serif">'
    . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fbf5ee"><tr><td align="center" style="padding:32px 16px">'
    . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px">'
    // En-tête : logo + nom
    . '<tr><td align="center" style="padding:0 0 22px">'
    . '<img src="https://' . SITE . '/email-logo.png" width="72" height="68" alt="Canifelin Harmonie" style="display:block;border:0;margin:0 auto 10px">'
    . '<div style="font-size:15px;font-weight:600;color:#0f2c3d">Canifelin Harmonie</div>'
    . '</td></tr>'
    // Carte
    . '<tr><td style="background:#ffffff;border-radius:20px;padding:32px 28px">'
    . '<h1 style="margin:0 0 6px;font-size:22px;line-height:1.25;font-weight:600;color:#0f2c3d;text-align:center">Nouvelle demande de rendez-vous</h1>'
    . '<p style="margin:0 0 24px;font-size:14px;color:#566673;text-align:center">Reçue via le site le ' . $h(date('d.m.Y à H:i')) . '</p>'
    . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
    . $row('Nom', $h($nom))
    . $row('E-mail', '<a href="mailto:' . $h($email) . '" style="color:#2770a6;text-decoration:none">' . $h($email) . '</a>')
    . $row('Téléphone', $telLink)
    . $row('Animal', $h($animal))
    . $row('Prestation', $h($prestation))
    . $row('Nom, âge, race', $h($or($infosAnimal)))
    . '</table>'
    . '<p style="margin:24px 0 8px;font-size:14px;font-weight:600;color:#0f2c3d">Message</p>'
    . '<div style="background:#fbf5ee;border-radius:14px;padding:16px 18px;font-size:15px;line-height:1.6;color:#1a2a35">'
    . nl2br($h($message)) . '</div>'
    . '<table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:28px auto 4px"><tr>'
    . '<td style="background:#3185c5;border-radius:999px"><a href="' . $h($replyHref) . '" style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none">Répondre à ' . $h($nom) . '</a></td>'
    . '</tr></table>'
    . '</td></tr>'
    // Origine du contact
    . '<tr><td style="padding:22px 28px 0">'
    . '<p style="margin:0 0 6px;font-size:13px;font-weight:600;color:#0f2c3d">Origine du contact</p>'
    . '<table role="presentation" cellpadding="0" cellspacing="0">'
    . $small('Comment m’avez-vous connue', $or($source))
    . $small('Arrivée sur la page', $or($arrivee))
    . $small('Venu de', $or($provenance))
    . $small('Campagne', $or($campagne))
    . $small('Formulaire rempli sur', $or($page))
    . '</table>'
    . '<p style="margin:18px 0 0;font-size:12px;color:#8a97a1;text-align:center">Vous pouvez aussi simplement cliquer sur « Répondre » : la réponse ira à ' . $h($email) . '.</p>'
    . '</td></tr>'
    . '</table></td></tr></table></body></html>';

$headers = implode("\r\n", [
    'From: ' . mb_encode_mimeheader('Site Canifelin Harmonie', 'UTF-8') . ' <' . FROM . '>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: canifelinharmonie.ch',
]);

// Priorité à l'envoi SMTP authentifié (mail.infomaniak.com:587 STARTTLS) si SMTP_PASS est défini
// sur le serveur (.env ou variable d'environnement) ; repli sur mail() natif sinon.
$sent = send_smtp(TO, $email, $subject, $body, $html);
// mail() est désactivée sur certains hébergements Infomaniak : on ne l'appelle que si elle existe
if (!$sent && function_exists('mail')) {
    $sent = @mail(TO, mb_encode_mimeheader($subject, 'UTF-8'), $body, $headers, '-f' . FROM);
}
if (!$sent) {
    reply(false, 500, 'mail');
}

$hits[] = $now;
@file_put_contents($bucket, implode(',', $hits), LOCK_EX);

reply(true);
