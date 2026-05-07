# UDBL TFC Manager

Plateforme web de gestion des Travaux de Fin de Cycle (TFC) pour l UDBL.

Le systeme couvre tout le cycle de vie d un TFC:

- soumission du sujet par l'étudiant,
- validation ou rejet par le chef de département,
- assignation de l'enseignant encadreur,
- depot des versions jury et finale,
- analyse IA automatique du document,
- autorisation et planification de la soutenance,
- publication des archives publiques des travaux defendus.

## Fonctionnalites principales

- Gestion des sujets avec workflow metier complet (pending, validated, rejected).
- Wizard de soumission etudiant en 5 etapes.
- Upload PDF (20 Mo max) pour version jury et version finale.
- Analyse IA via GPTZero (ou mode simulation si la cle API est absente).
- Gestion des roles et permissions avec Spatie Laravel Permission:
   - Admin
   - Chef de département
   - Enseignant
   - Etudiant
- Tableau de bord dynamique selon le role.
- Notifications in-app (lecture, suppression, marquer tout comme lu).
- Administration complete:
  - utilisateurs,
  - facultes,
  - filieres,
  - annees academiques,
  - parametres systeme,
  - journal d activite.
- Archives publiques des travaux defendus avec telechargement de la version finale.

## Regles d acces importantes

- L inscription publique est desactivee.
- Les comptes sont crees par l administrateur depuis le module Admin.
- Le profil utilisateur est en consultation (lecture seule).
- La reinitialisation du mot de passe est geree par l administrateur.

## Stack technique

- PHP 8.2+
- Laravel 12
- PostgreSQL 14+
- Blade + Tailwind CSS + Alpine.js
- Vite
- Spatie Laravel Permission
- smalot/pdfparser

## Prerequis

- PHP 8.2 ou plus
- Composer 2.x
- Node.js 18+ et npm
- PostgreSQL 14+

## Installation rapide

1. Cloner le projet

   git clone <URL_DU_REPO>
   cd udbl-tfc-manager

2. Installer les dependances

   composer install
   npm install

3. Initialiser la configuration

   copy .env.example .env
   php artisan key:generate

4. Configurer la base PostgreSQL dans .env

   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=udbl_tfc_manager
   DB_USERNAME=postgres
   DB_PASSWORD=

5. Migrer et charger les donnees

   php artisan migrate --seed

6. Lier le stockage public

   php artisan storage:link

7. Construire les assets

   npm run build

8. Lancer l application

   php artisan serve

Application disponible sur:

<http://127.0.0.1:8000>

## Mode developpement

- Lancer tout le stack local (serveur, queue, logs, vite):

  composer dev

- Ou separer les processus:

  php artisan serve
  php artisan queue:listen --tries=1 --timeout=0
  npm run dev

## Comptes de demonstration (seeders)

Mot de passe par defaut:
password

Exemples de comptes:

- Admin: <admin@udbl-tfc.cd>
- Chef de département: <cp.gl@udbl-tfc.cd>
- Enseignant: <prof1@udbl-tfc.cd>
- Etudiant: <etudiant1@udbl-tfc.cd>

## Analyse IA

Variables .env utiles:

- AI_DETECTION_API_KEY
- AI_DETECTION_API_URL

Comportement:

- si AI_DETECTION_API_KEY est vide, le systeme passe en mode simulation,
- sinon, l application utilise GPTZero pour l analyse.

## Tests

- Lancer tous les tests:

  php artisan test

- Ou via Composer:

  composer test

## Documentation projet

- TFC-DOCUMENT.md
- RAPPORT-COMPLET.md
- DIAGRAMMES-DESCRIPTIONS.md
- DIAGRAMMES-PAR-CHAPITRE-NB.md
- DIAGRAMMES-PAR-FIGURE-NB.md
