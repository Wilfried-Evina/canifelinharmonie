// Données centrales du site : modifier ici met à jour toutes les pages.

export const site = {
  name: 'Canifelin Harmonie',
  owner: 'Brigitte Trachsel',
  tagline: 'Comprendre, respecter, accompagner',
  description:
    'Éducatrice canin agréée SCAV (TMC) et intervenante en comportement chien et chat à Genève : éducation, comportement, soins coopératifs et garde à domicile. Accompagnement respectueux et scientifiquement fondé, Genève et environs.',
  url: 'https://canifelinharmonie.ch',
  email: 'contact@canifelinharmonie.ch',
  phoneDisplay: '+41 79 209 66 22',
  phoneIntl: '+41792096622',
  // Message pré-rempli : permet de savoir qu'un contact WhatsApp vient du site
  whatsapp: `https://wa.me/41792096622?text=${encodeURIComponent('Bonjour Brigitte, je vous contacte depuis votre site canifelinharmonie.ch. ')}`,
  city: 'Genève',
  base: 'Grand-Lancy',
  street: 'Av. des Communes-Réunies 58',
  postalCode: '1212',
  /** Adresse complète sur une ligne */
  address: 'Av. des Communes-Réunies 58, 1212 Grand-Lancy',
  mapsUrl: 'https://www.google.com/maps/search/?api=1&query=Av.+des+Communes-R%C3%A9unies+58%2C+1212+Grand-Lancy',
  region: 'Canton de Genève',
  radiusKm: 15,
  kmFee: 'CHF 0.90 / km',
  responseTime: '48 h ouvrables',
  payment: ['Virement bancaire', 'TWINT', 'Espèces'],
};

export const nav = [
  { href: '/chien', label: 'Chien' },
  { href: '/chat', label: 'Chat' },
  { href: '/garde-a-domicile', label: 'Garde à domicile' },
  { href: '/tarifs', label: 'Tarifs' },
  { href: '/qui-suis-je', label: 'Qui suis-je' },
];

export type Price = {
  name: string;
  duration: string;
  price: string;
  unit: string;
  extra?: string;
  /** Petite mention sous la liste (conditions, km inclus…) */
  note?: string;
  features: string[];
  featured?: boolean;
};

export const consultations: { chien: Price[]; chat: Price[] } = {
  chien: [
    {
      name: 'Consultation comportement',
      duration: '1 h 30',
      price: '120',
      unit: 'la séance',
      featured: true,
      features: [
        'Première consultation à domicile ou à Genève centre',
        'Analyse de l’environnement, de l’histoire et de la relation',
        'Rapport écrit, transmis à votre vétérinaire si vous le souhaitez',
      ],
    },
    {
      name: 'Cours d’éducation',
      duration: 'Dès 45 min',
      price: '60',
      unit: 'la séance',
      features: [
        'Séance pratique avec vous et votre chien',
        'Exercices concrets à reproduire au quotidien',
        'Idéal en suivi après une consultation',
      ],
    },
  ],
  chat: [
    {
      name: 'Consultation',
      duration: '1 h',
      price: '80',
      unit: 'de l’heure',
      features: [
        'Conseils ciblés sur une problématique',
        'Éducation, enrichissement, aménagement',
        'À domicile ou en ligne',
      ],
    },
    {
      name: 'Consultation complète',
      duration: '1 h 30',
      price: '130',
      unit: 'la séance',
      featured: true,
      features: [
        'Bilan comportemental approfondi à domicile',
        'Compte rendu écrit avec plan d’action personnalisé',
        'Appel de suivi 15 jours plus tard, inclus',
      ],
    },
  ],
};

// Tarifs de garde : documents « Offre prestations canin / félins » (août 2026)
const gardeChat = [
  'Nettoyage des litières et des salissures éventuelles',
  'Repas selon le régime et eau fraîche chaque jour',
  'Interaction selon ses préférences : câlins, jeux ou présence sans contact',
  'Surveillance et lien avec votre vétérinaire si nécessaire',
  'Nouvelles et photos chaque jour (e-mail ou WhatsApp)',
  'Bilan de garde écrit en fin de séjour',
];

export const garde: { chiens: Price[]; chats: Price[] } = {
  chiens: [
    {
      name: 'Offre de base',
      duration: '30 min',
      price: '30',
      unit: 'par promenade',
      features: [
        'Promenade du chien',
        'Eau fraîche et repas de mi-journée selon son régime',
        'Interaction selon ses préférences (câlins, jeu, présence sans contact)',
        'Observation de son comportement et bilan régulier',
      ],
      note: 'Idéal pour une sortie quotidienne pendant vos heures de travail.',
    },
    {
      name: 'Offre confort',
      duration: '45 à 60 min',
      price: '45',
      unit: 'par passage',
      extra: '+ CHF 20 par heure supplémentaire de présence',
      featured: true,
      features: [
        'Promenade, eau fraîche et repas selon son régime',
        'Interaction selon ses préférences',
        'Surveillance et lien avec votre vétérinaire si nécessaire',
        'Nouvelles et photos chaque jour (e-mail ou WhatsApp)',
        'Bilan de garde écrit',
      ],
      note: 'Pour une absence de plusieurs jours : au moins 3 visites par jour, absence de 4 jours maximum.',
    },
    {
      name: 'Offre famille',
      duration: '45 à 60 min',
      price: '65',
      unit: 'par passage',
      extra: '+ CHF 20 par heure supplémentaire de présence',
      features: [
        'Chien et chat, ex. 1 chien + 1 chat',
        'Promenade, repas, eau fraîche et litières',
        'Interaction avec chaque animal selon ses préférences',
        'Nouvelles et photos chaque jour (e-mail ou WhatsApp)',
        'Bilan de garde écrit',
      ],
      note: 'Au moins 3 visites par jour, absence de 4 jours maximum. Adaptable selon le groupe d’animaux.',
    },
  ],
  chats: [
    {
      name: 'Offre de base',
      duration: '30 min',
      price: '35',
      unit: 'par jour',
      features: gardeChat,
    },
    {
      name: 'Offre confort',
      duration: '45 à 60 min',
      price: '45',
      unit: 'par jour',
      featured: true,
      features: gardeChat,
      note: 'Plus de temps pour le jeu et les câlins.',
    },
    {
      name: 'Offre famille',
      duration: '1 h',
      price: '65',
      unit: 'par jour',
      features: ['De 2 à 5 chats', ...gardeChat],
    },
  ],
};

export const formations = [
  { year: '2024', title: 'L’agression chez le chien', org: 'Michael Shikashio' },
  {
    year: '2022 – 2025',
    title: 'Cursus d’entraînement coopératif et medical training',
    org: 'Educhateur France · Cyrielle Siméon',
  },
  { year: '2021', title: 'Diplôme d’Éducateur Canin (DIC)', org: 'Certification OSAV' },
  { year: '2020', title: 'Intervenante en comportement félin', org: 'Educhateur Québec' },
  { year: '2019', title: 'Coach en comportement', org: 'Dr Joël Dehasse' },
  { year: '2019', title: 'Garde d’animaux (FSIP)', org: 'Certification OSAV' },
];

export const formationsNote =
  'Cette liste évolue régulièrement : la formation continue fait partie intégrante de ma pratique professionnelle.';

export const faq = [
  {
    q: 'Où se déroulent les consultations ?',
    a: 'Le plus souvent à votre domicile : c’est là que votre animal vit et que les difficultés apparaissent. Pour les chiens, une séance peut aussi avoir lieu à Genève centre ou en extérieur. Certaines consultations pour chats sont possibles en ligne.',
  },
  {
    q: 'Quelle zone couvrez-vous ?',
    a: 'Genève et ses environs. Les déplacements sont inclus dans un rayon de 15 km autour de Grand-Lancy ; au-delà, ils sont facturés CHF 0.90 par kilomètre.',
  },
  {
    q: 'Faut-il consulter un vétérinaire avant ?',
    a: 'Oui, c’est recommandé. Un changement de comportement peut avoir une cause médicale (douleur, problème urinaire…). Je ne pose pas de diagnostic médical et je travaille en lien avec votre vétérinaire si besoin.',
  },
  {
    q: 'Quelles méthodes utilisez-vous ?',
    a: 'Des méthodes bienveillantes et fondées sur la science du comportement : renforcement positif, coopération, respect des besoins de l’espèce. Aucune méthode coercitive, aucun matériel aversif.',
  },
  {
    q: 'Combien de séances faut-il prévoir ?',
    a: 'Cela dépend de la problématique. Une première consultation permet de poser un bilan et un plan clair ; certaines situations se règlent en une séance, d’autres demandent un suivi sur quelques semaines.',
  },
  {
    q: 'Comment puis-je payer ?',
    a: 'Par virement bancaire, TWINT ou en espèces. Lors des visites à domicile, TWINT et espèces sont les plus pratiques.',
  },
];

export type UsefulLink = { name: string; note: string; href?: string };

export const usefulLinks: { category: string; items: UsefulLink[] }[] = [
  {
    category: 'Vétérinaires comportementalistes',
    items: [
      {
        name: 'Dr Ariane Maillard & Dr Stéphane Bleuer-Elsner',
        note: 'Psychiatrie vétérinaire et médecine du comportement à Genève',
        href: 'https://www.vetelys.ch',
      },
    ],
  },
  {
    category: 'Ostéopathie animale',
    items: [
      { name: 'Dr Auxanne Lepori-Fellay', note: 'Vétérinaire ostéopathe', href: 'https://www.vetosteopattes.ch' },
      {
        name: 'Ostéo Animal’Care Suisse',
        note: 'Ostéopathe animalier pour chiens, chats, chevaux et NAC',
        href: 'https://www.osteo-animalcare.ch',
      },
    ],
  },
  {
    category: 'Centre hospitalier vétérinaire',
    items: [
      {
        name: 'OnlyVet',
        note: 'Centre hospitalier vétérinaire à Saint-Priest, près de Lyon : chirurgie, orthopédie, oncologie… Uniquement sur référence de votre vétérinaire.',
      },
    ],
  },
  {
    category: 'Mastication',
    items: [{ name: 'Canifeeling', note: 'Amuse-gueule et produits de mastication naturels' }],
  },
  {
    category: 'Sécurité fenêtres et balcons',
    items: [
      {
        name: 'ProtectionChat',
        note: 'Installation de filets de protection pour chats en Suisse et France voisine',
        href: 'https://www.protectionchat.ch',
      },
    ],
  },
];
