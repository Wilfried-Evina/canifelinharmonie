# Canifelin Harmonie

Site de Brigitte Trachsel, comportementaliste chien & chat à Genève. Construit avec [Astro](https://astro.build) : site statique, rapide, sans cookies.

## Démarrer

```bash
npm install
npm run dev      # http://localhost:4321
npm run build    # génère le site dans dist/
npm run preview  # prévisualise le build
```

## Où modifier quoi

| Quoi | Fichier |
|---|---|
| Coordonnées, tarifs, formations, FAQ, liens utiles | `src/data/site.ts` |
| Photos et textes alternatifs | `src/data/photos.ts` + `src/assets/photos/` |
| Couleurs, typographies, styles communs | `src/styles/global.css` |
| Pages | `src/pages/*.astro` |
| Redirections des anciennes URL | `astro.config.mjs` |

## Formulaire de contact

Sans configuration, le formulaire ouvre la messagerie du visiteur avec un e-mail pré-rempli.
Pour un envoi direct, créer un formulaire sur Formspree / Web3Forms et renseigner `PUBLIC_FORM_ENDPOINT` (voir `.env.example`).

## Déploiement (Infomaniak)

1. `npm run build` : génère le site dans `dist/` (y compris `dist/.htaccess`, fichier caché).
2. Dans le Manager Infomaniak : Hébergement Web → le site → **FTP / SSH** pour récupérer les accès.
3. Envoyer **le contenu** de `dist/` (pas le dossier lui-même) dans le dossier racine du site
   (`/sites/canifelinharmonie.ch/` ou `/web/` selon l'hébergement), avec FileZilla ou Cyberduck.
   Penser à afficher les fichiers cachés pour bien envoyer `.htaccess`.
4. Activer le certificat SSL gratuit (Let's Encrypt) dans le Manager si ce n'est pas déjà fait.

`public/.htaccess` gère : HTTPS obligatoire, domaine sans www, redirections 301 des anciennes URL,
page 404, compression et cache.

## Formulaire de contact

- En production, le formulaire envoie les demandes via `public/envoi-contact.php` (copié dans `dist/`), directement à contact@canifelinharmonie.ch grâce au PHP d’Infomaniak. Aucun service tiers.
- Chaque demande contient l’origine du contact (page d’arrivée, site de provenance, campagne utm, « Comment m’avez-vous connue ? »).
- En local (`npm run dev`), PHP ne tourne pas : le formulaire ouvre un e-mail pré-rempli. Si l’envoi échoue en ligne, le visiteur voit un lien e-mail pré-rempli et WhatsApp : aucune demande perdue.
- **À tester après la mise en ligne** : envoyer une demande d’essai et vérifier qu’elle arrive (et pas dans les indésirables).
