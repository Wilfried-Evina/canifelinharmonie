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

date_default_timezone_set('Europe/Zurich');
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

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

$headers = implode("\r\n", [
    'From: ' . mb_encode_mimeheader('Site Canifelin Harmonie', 'UTF-8') . ' <' . FROM . '>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: canifelinharmonie.ch',
]);

$sent = @mail(TO, mb_encode_mimeheader($subject, 'UTF-8'), $body, $headers, '-f' . FROM);
if (!$sent) {
    reply(false, 500, 'mail');
}

$hits[] = $now;
@file_put_contents($bucket, implode(',', $hits), LOCK_EX);

reply(true);
