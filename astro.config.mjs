// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

const excludedFromSitemap = [
  '/prestations-chiens',
  '/prestations-chats',
  '/commande-chien',
  '/commande-chat',
  '/liens-utile',
  '/404',
];

export default defineConfig({
  site: 'https://canifelinharmonie.ch',
  integrations: [
    sitemap({
      // comparaison exacte du chemin (« /liens-utile » ne doit pas exclure « /liens-utiles »)
      filter: (page) => !excludedFromSitemap.includes(new URL(page).pathname.replace(/\/$/, '')),
      serialize: (item) => {
        const url = item.url.replace(/\/$/, '');
        const highPriority = [
          'https://canifelinharmonie.ch',
          'https://canifelinharmonie.ch/chien',
          'https://canifelinharmonie.ch/chat',
          'https://canifelinharmonie.ch/garde-a-domicile',
          'https://canifelinharmonie.ch/tarifs',
          'https://canifelinharmonie.ch/contact',
        ];
        const legalPages = [
          'https://canifelinharmonie.ch/conditions-d-utilisation',
          'https://canifelinharmonie.ch/politique-de-confidentialite',
        ];
        return {
          ...item,
          lastmod: new Date().toISOString(),
          priority: highPriority.includes(url) ? 1.0 : legalPages.includes(url) ? 0.3 : 0.7,
        };
      },
    }),
  ],
  // Anciennes URL du site -> nouvelles pages (préserve le référencement)
  redirects: {
    '/prestations-chiens': '/garde-a-domicile#chiens',
    '/prestations-chats': '/garde-a-domicile#chats',
    '/commande-chien': '/contact?animal=chien',
    '/commande-chat': '/contact?animal=chat',
    '/liens-utile': '/liens-utiles',
  },
});
