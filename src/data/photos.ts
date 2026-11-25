// Registre des photos du site (originaux HD fournis par Brigitte, métadonnées supprimées).
// Pour ajouter une photo : la déposer dans src/assets/photos/, l'importer ici, puis l'ajouter à une liste.

// --- Brigitte & sa famille
import brigitte from '../assets/photos/brigitte-et-ses-leonbergs.jpg';
import trio from '../assets/photos/trio-namak-diablotine-olympea.jpg';
import namakDiablotineOlympea from '../assets/photos/namak-diablotine-olympea.jpg';
import leonbergChiots from '../assets/photos/leonberg-et-chiots.jpg';
import regardNamak from '../assets/photos/regard-de-namak.jpg';
import diablotine from '../assets/photos/diablotine.jpg';
import olympeaDiablotine from '../assets/photos/olympea-et-diablotine.jpg';

// --- Chiens
import seanceEducation from '../assets/photos/seance-education-leonbergs.jpg';
import namakForet from '../assets/photos/namak-leonberg-foret.jpg';
import namakChiot from '../assets/photos/namak-chiot-leonberg.jpg';
import namakChapeau from '../assets/photos/namak-chapeau.jpg';
import namakKira from '../assets/photos/namak-et-kira.jpg';
import namakBouvier from '../assets/photos/namak-et-bouvier-bernois.jpg';
import bandit from '../assets/photos/bandit-jack-russell.jpg';
import chiensParc from '../assets/photos/chiens-jeu-parc.jpg';
import olaf from '../assets/photos/olaf-chien-noir-feu.jpg';
import olafPortrait from '../assets/photos/olaf-portrait.jpg';
import bergerBlanc from '../assets/photos/berger-blanc-et-chiot.jpg';
import leonbergs from '../assets/photos/leonbergs-groupe.jpg';
import leonbergsRepos from '../assets/photos/leonbergs-au-repos.jpg';
import leonbergsVoiture from '../assets/photos/leonbergs-voiture.jpg';
import kiara from '../assets/photos/kiara-berger.jpg';
import sociabilisation from '../assets/photos/sociabilisation-chiens.jpg';
import chiensNeige from '../assets/photos/chiens-neige.jpg';
import entente from '../assets/photos/entente-chien-et-chat.jpg';

// --- Soins coopératifs / medical training
import namakVeto from '../assets/photos/namak-soins-cooperatifs-veterinaire.jpg';
import namakLunettes from '../assets/photos/namak-lunettes-physiotherapie.jpg';
import namakTable from '../assets/photos/namak-medical-training-table.jpg';
import namakMaison from '../assets/photos/namak-medical-training-maison.jpg';
import diablotineMedical from '../assets/photos/diablotine-medical-training.jpg';

// --- Chats
import maineCoons from '../assets/photos/maine-coons-groupe.jpg';
import praline from '../assets/photos/praline-et-vanille-chatons.jpg';
import maineCoonRoux from '../assets/photos/maine-coon-roux.jpg';
import maineCoonBleu from '../assets/photos/maine-coon-bleu.jpg';
import maineCoonGris from '../assets/photos/maine-coon-gris.jpg';
import maineCoonCabane from '../assets/photos/maine-coon-roux-cabane.jpg';
import maineCoonBonnet from '../assets/photos/maine-coon-bonnet.jpg';
import maineCoonHerbe from '../assets/photos/maine-coon-roux-herbe.jpg';
import tigrou from '../assets/photos/tigrou-chat-angora.jpg';
import tigrouChaton from '../assets/photos/tigrou-chaton.jpg';
import macha from '../assets/photos/macha-chat-tigre.jpg';
import chatons from '../assets/photos/chatons-arbre-a-chat.jpg';
import uka from '../assets/photos/mamie-uka-ecaille.jpg';
import troisChats from '../assets/photos/trois-chats-jardin.jpg';
import tunnel from '../assets/photos/chats-tunnel-jeu.jpg';
import tapisFouille from '../assets/photos/tapis-de-fouille-chats.jpg';
import arbreAChat from '../assets/photos/diablotine-olympea-arbre-a-chat.jpg';
import chatTigre from '../assets/photos/chat-tigre-gros-plan.jpg';
import chatsTerrasse from '../assets/photos/chats-terrasse.jpg';

export const dogPhotos = [
  { src: leonbergs, alt: 'Quatre Léonbergs assis dans l’herbe', caption: 'Ladakh & cie' },
  { src: namakChiot, alt: 'Namak, chiot Léonberg, tirant la langue dans l’herbe', caption: 'Namak bébé' },
  { src: bandit, alt: 'Bandit, Jack Russell blanc et marron, couché sur une terrasse', caption: 'Bandit' },
  { src: kiara, alt: 'Kiara, berger noir et feu, souriante dans un pré fleuri', caption: 'Kiara' },
  { src: sociabilisation, alt: 'Plusieurs Léonbergs se rencontrent dans un pré : séance de sociabilisation', caption: 'Sociabilisation' },
  { src: olafPortrait, alt: 'Olaf, jeune chien noir et feu, portrait', caption: 'Olaf' },
  { src: namakKira, alt: 'Namak, Léonberg, et Kira, chienne blanche et fauve, au bord d’un chemin', caption: 'Namak & Kira' },
  { src: chiensNeige, alt: 'Chiens qui jouent ensemble dans la neige en forêt', caption: 'Jeux dans la neige' },
  { src: namakChapeau, alt: 'Namak, Léonberg, coiffé d’un chapeau en papier, la langue sortie', caption: 'Namak fait la fête' },
  { src: leonbergsRepos, alt: 'Quatre Léonbergs couchés à l’ombre dans un jardin', caption: 'Pause à l’ombre' },
  { src: bergerBlanc, alt: 'Bosco, grand Berger blanc suisse, et N’Onyx, petit chien noir, assis au bord d’une rivière', caption: 'Bosco & N’Onyx' },
  { src: leonbergsVoiture, alt: 'Trois Léonbergs installés dans le coffre d’une voiture', caption: 'En route !' },
];

export const catPhotos = [
  { src: olympeaDiablotine, alt: 'Olympea, Maine Coon argenté, et Diablotine, chatte tricolore, sur un lit', caption: 'Olympea & Diablotine' },
  { src: maineCoonBonnet, alt: 'Maine Coon roux coiffé d’un bonnet de Noël, couché dans un panier', caption: 'Esprit de Noël' },
  { src: praline, alt: 'Praline et Vanille, deux chatonnes tricolores sur un canapé', caption: 'Praline & Vanille' },
  { src: maineCoonBleu, alt: 'Maine Coon bleu assis dans un jardin', caption: 'Au jardin' },
  { src: tunnel, alt: 'Deux chats qui jouent avec un tunnel en tissu sur un parquet', caption: 'L’heure du jeu' },
  { src: tigrou, alt: 'Tigrou, chat à poil long aux yeux verts', caption: 'Tigrou' },
  { src: maineCoonCabane, alt: 'Maine Coon roux et blanc assis devant sa cabane d’extérieur', caption: 'Sur la terrasse' },
  { src: macha, alt: 'Macha, chatte tigrée grise, couchée sur une serviette', caption: 'Macha' },
  { src: chatons, alt: 'Deux chatons, un tigré et un gris et blanc, sur un arbre à chat', caption: 'Tigrou & Plume' },
  { src: troisChats, alt: 'Trois Maine Coons réunis dans un jardin ensoleillé', caption: 'Le trio' },
  { src: maineCoonGris, alt: 'Maine Coon gris allongé, pattes en avant, sur un lit', caption: 'Majestueux' },
  { src: uka, alt: 'Mamie Uka, chatte écaille de tortue, assise devant un grillage', caption: 'Mamie Uka' },
];

export const photos = {
  // famille
  brigitte,
  trio,
  namakDiablotineOlympea,
  leonbergChiots,
  regardNamak,
  diablotine,
  olympeaDiablotine,
  // chiens
  seanceEducation,
  bergerBlanc,
  namakBouvier,
  olaf,
  namakForet,
  namakChiot,
  bandit,
  chiensParc,
  leonbergs,
  leonbergsRepos,
  kiara,
  sociabilisation,
  chiensNeige,
  namakKira,
  namakChapeau,
  entente,
  // soins coopératifs
  namakVeto,
  namakLunettes,
  namakTable,
  namakMaison,
  diablotineMedical,
  // chats
  tunnel,
  macha,
  maineCoonCabane,
  uka,
  chatons,
  maineCoons,
  praline,
  maineCoonRoux,
  maineCoonHerbe,
  tigrou,
  tigrouChaton,
  troisChats,
  tapisFouille,
  arbreAChat,
  chatTigre,
  chatsTerrasse,
};
