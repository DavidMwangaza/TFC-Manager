# RAPPORT COMPLET — UDBL-TFC-MANAGER

> **Application web de gestion des Travaux de Fin de Cycle**
> Université UDBL — février 2026
> Généré le 12 février 2026

---

## 1. PRÉSENTATION DU PROJET

**UDBL TFC Manager** est une application web permettant de gérer l'intégralité du cycle de vie des Travaux de Fin de Cycle (TFC) à l'Université UDBL : de la soumission du sujet par l'étudiant, en passant par la validation par le chef de département, l'assignation d'un encadreur, le dépôt du document PDF, l'analyse IA anti-plagiat, jusqu'à l'autorisation (et son retrait contrôlé) puis la planification de la soutenance.

### Objectifs principaux

- Dématérialiser la gestion des sujets de TFC
- Automatiser le workflow de validation (soumission, validation, rejet)
- Intégrer un service de détection IA / plagiat
- Offrir un tableau de bord adapté à chaque rôle
- Centraliser l'administration (utilisateurs, filières, années académiques)

---

## 2. STACK TECHNIQUE

| Technologie | Version | Rôle |
|---|---|---|
| **PHP** | ^8.2 | Langage serveur |
| **Laravel** | ^12.0 | Framework PHP |
| **Laravel Breeze** | * | Authentification (Blade stack) |
| **Spatie Laravel Permission** | ^7.0 | Gestion des rôles et permissions |
| **smalot/pdfparser** | ^2.12 | Extraction de texte depuis les PDF |
| **Tailwind CSS** | ^3.1 / @tailwindcss/vite ^4.0 | Framework CSS |
| **Alpine.js** | ^3.4 | Interactivité JavaScript (wizard, toggles) |
| **Vite** | ^7.0 | Build frontend |
| **Chart.js** | CDN | Graphiques dashboard admin |
| **PostgreSQL** | 14+ | Base de données relationnelle utilisée par l'application |

---

## 3. ARCHITECTURE DE L'APPLICATION

### 3.1 Comptage des fichiers

| Element | Nombre |
|---|---|
| Contrôleurs | **12** |
| Méthodes (total) | **47** |
| Modèles Eloquent | **9** |
| Vues Blade | **53** |
| Composants Blade | **15** |
| Notifications | **6** |
| Middleware personnalisé | **1** |
| Seeders | **3** |
| Migrations | **20** |
| Icônes SVG (Heroicons) | **43** |
| Routes | **58** |

### 3.2 Contrôleurs et méthodes

| Contrôleur | Méthodes | Description |
|---|---|---|
| `DashboardController` | 5 | Dashboard dynamique par rôle (admin, CP, enseignant, étudiant) |
| `SubjectController` | 10 | CRUD sujets, validation, rejet, export CSV, autorisation/retrait du feu vert et planification soutenance |
| `ThesisFileController` | 3 | Upload PDF, téléchargement, analyse IA |
| `NotificationController` | 5 | Liste, marquer lu, marquer tout lu, suppression |
| `ProfileController` | 1 | Consultation du profil (lecture seule) |
| `Admin\UserController` | 8 | CRUD utilisateurs, blocage, reset mot de passe |
| `Admin\FacultyController` | 6 | CRUD facultés |
| `Admin\DepartmentController` | 6 | CRUD filières |
| `Admin\AcademicYearController` | 6 | Années académiques (créer, définir courante, clôturer) |
| `Admin\SettingController` | 2 | Paramètres système |
| `Admin\LogController` | 1 | Journal d'activité |
| `Auth\RegisteredUserController` | 2 | Contrôleur d'inscription étudiant (présent mais route publique désactivée) |

### 3.3 Modèles de données

| Modèle | Champs principaux | Relations |
|---|---|---|
| **User** | name, email, matricule, department_id, is_blocked | department, subjects, supervisedSubjects |
| **Subject** | title, subject_type, description, 10 champs structurés, status, defense_validated, defense_date, defense_room | student, teacher, department, academicYear, thesisFiles |
| **Faculty** | name, code, description | departments |
| **Department** | faculty_id, name, code, description | users, subjects |
| **ThesisFile** | subject_id, file_path, original_name, version_type | subject, aiReport |
| **AiReport** | thesis_file_id, similarity_score, ai_score, details | thesisFile |
| **AcademicYear** | name, start_date, end_date, is_current, is_closed | subjects |
| **SystemSetting** | key, value, type, group, label, description | — |
| **ActivityLog** | user_id, action, model_type, description, old/new_values | user |

### 3.4 Vues Blade (53 fichiers)

| Répertoire | Fichiers | Détail |
|---|---|---|
| `admin/` | 12 | Dashboard, users (index/create/edit), departments (index/create/edit), academic-years (index/create), settings, logs, sidebar |
| `auth/` | 6 | Login, forgot-password, reset-password, verify-email, confirm-password (+ vue register non exposée) |
| `components/` | 15 | icon, breadcrumb, modal, boutons, inputs, dropdown, nav-link, etc. |
| `cp/` | 1 | Dashboard Chef de département |
| `errors/` | 4 | Pages 403, 404, 419, 500 (en français) |
| `layouts/` | 3 | app, guest, navigation |
| `notifications/` | 1 | Liste des notifications |
| `profile/` | 4 | Page profil active (lecture seule) + partiels legacy non exposés |
| `student/` | 1 | Dashboard étudiant |
| `subjects/` | 3 | Index (liste + filtres), create (wizard 5 étapes), show (détail) |
| `teacher/` | 1 | Dashboard enseignant |
| `racine` | 2 | welcome, dashboard (redirection) |

---

## 4. SYSTEME DE ROLES ET ACCES

### 4.1 Rôles (4)

| Rôle | Permissions |
|---|---|
| **Admin** | Toutes les 17 permissions |
| **Chef de département** | subjects.view, subjects.validate, subjects.reject, subjects.assign-teacher, thesis.download, thesis.view-reports |
| **Enseignant** | subjects.view, thesis.download, thesis.view-reports, thesis.validate-defense |
| **Etudiant** | subjects.create, subjects.view, thesis.upload, thesis.final-deposit |

### 4.2 Permissions (17)

`subjects.create`, `subjects.view`, `subjects.validate`, `subjects.reject`, `subjects.assign-teacher`, `thesis.upload`, `thesis.download`, `thesis.view-reports`, `thesis.final-deposit`, `thesis.validate-defense`, `users.manage`, `departments.manage`, `academic-years.manage`, `settings.manage`, `logs.view`, `users.block`, `users.reset-password`

### 4.3 Middleware de sécurité

| Middleware | Portée | Rôle |
|---|---|---|
| `CheckUserBlocked` | Global (groupe web) | Déconnecte les utilisateurs bloqués |
| `role:Admin` | Routes `/admin/*` | Accès administration |
| `role:Etudiant` | Création sujet + upload | Accès étudiant |
| `role:Chef de département` | Validation/rejet | Accès chef de département |
| `role:Enseignant` | Autorisation/retrait soutenance | Accès enseignant |
| `auth` | Toutes routes internes | Authentification requise |

### 4.4 Création des comptes

- L'inscription publique (`/register`) est actuellement **désactivée**
- Tous les comptes sont créés par l'administrateur via la gestion des utilisateurs (`/admin/users`)
- Pour les étudiants, l'administrateur renseigne notamment : Nom, Email, Matricule (unique), Filière
- Le rôle approprié (`Etudiant`, `Enseignant`, `Chef de département`, `Admin`) est assigné par l'administrateur
- La réinitialisation des mots de passe est également gérée par l'administrateur

---

## 5. FONCTIONNALITES PAR ROLE

### 5.1 Etudiant

- Soumission de sujet via **wizard en 5 étapes** (Alpine.js) :
  1. Informations générales (titre, type TFC/Mémoire)
  2. Contexte et problématique (contexte, défis, question de recherche)
  3. Hypothèse et objectifs (hypothèse, objectif général, objectifs spécifiques)
  4. Etat de l'art (auteurs, institutions, contributions)
  5. Méthodologie et démarcation + récapitulatif
- Upload du fichier TFC en PDF (max 20 Mo, version jury + version finale)
- Consultation de sa progression (sujet, statut, fichiers, analyse IA globale)
- Consultation de la date et de la salle de soutenance lorsqu'elles sont planifiées
- Notifications reçues : sujet validé, sujet rejeté, encadreur assigné, soutenance autorisée ou autorisation retirée

### 5.2 Chef de département

- Visualisation de tous les sujets de sa filière
- Validation d'un sujet avec assignation d'un enseignant encadreur
- Rejet d'un sujet avec motif obligatoire
- Planification de la soutenance (date et salle) après le feu vert
- Export CSV des sujets de sa filière
- Notifications reçues : nouveau sujet soumis

### 5.3 Enseignant

- Visualisation des sujets supervisés
- Téléchargement des fichiers TFC
- Consultation des scores IA (similarité + score IA) avec badges colorés
- Affichage de la date et de la salle de soutenance si planifiées
- Autorisation de soutenance
- Retrait du feu vert (possible uniquement avant dépôt de la version finale, avec motif obligatoire)
- Notifications reçues : assignation comme encadreur, nouveau fichier TFC déposé

### 5.4 Administrateur

- **Dashboard** avec graphiques Chart.js (doughnut statuts, barres par filière)
- **CRUD Utilisateurs** : créer, modifier, supprimer, bloquer/débloquer, réinitialiser mot de passe
- **CRUD Facultés** : nom + code + description
- **CRUD Filières** : faculté + nom + code + description
- **Années Académiques** : créer, définir l'année courante, clôturer
- **Paramètres système** : configuration clé/valeur
- **Journal d'activité** : toutes les actions admin sont loguées
- **Export CSV** des sujets (filtrable)

---

## 6. SYSTEME DE NOTIFICATIONS

| Notification | Canal | Déclencheur | Destinataire |
|---|---|---|---|
| `NewSubjectSubmitted` | database + mail | Etudiant soumet un sujet | Chef de département |
| `SubjectValidated` | database + mail | CP valide un sujet | Etudiant |
| `SubjectRejected` | database + mail | CP rejette un sujet | Etudiant |
| `TeacherAssigned` | database + mail | CP assigne un encadreur | Enseignant |
| `ThesisFileUploaded` | database + mail | Etudiant dépose un PDF | Enseignant encadreur |
| `DefenseAuthorized` | database + mail | Enseignant autorise la soutenance | Etudiant |
| `DefenseAuthorizationRevoked` | database + mail | Enseignant retire le Feu Vert avec motif (avant version finale) | Etudiant |

Les notifications apparaissent dans l'icône cloche de la barre de navigation (badge rouge avec compteur).

---

## 7. SERVICE DE DETECTION IA

### 7.1 Architecture

Le fichier `app/Services/AiDetectionService.php` gère :

1. **Extraction de texte** depuis le PDF via `smalot/pdfparser`
2. **Envoi à l'API** de détection (GPTZero)
3. **Stockage du rapport** dans la table `ai_reports`

### 7.2 Fournisseur supporte

| Fournisseur | Variable .env | URL API |
|---|---|---|
| **GPTZero** | `AI_DETECTION_API_KEY` | `https://api.gptzero.me/v2/predict/text` |
| **Simulation** | (vide ou non configuré) | Scores aléatoires pour le développement |

### 7.3 Configuration (.env)

```env
AI_DETECTION_API_KEY=          # Cle API du fournisseur (optionnelle)
AI_DETECTION_API_URL=https://api.gptzero.me/v2/predict/text
```

### 7.4 Comportement

- Si aucune clé API n'est configurée : **mode simulation** (scores aléatoires)
- Si l'API échoue (timeout, erreur HTTP) : **fallback automatique** vers la simulation
- Limite : 50 000 caractères envoyés à l'API
- Timeout : 60 secondes
- L'étudiant ne voit qu'un indicateur global (vert/jaune/rouge), pas les scores exacts
- L'enseignant et l'admin voient les scores détaillés

---

## 8. DONNEES INITIALES (SEEDERS)

### 8.1 Facultés et Filières (13 filières, 4 facultés)

| Faculté | Filières |
|---|---|
| **ESIS** (7) | Génie Logiciel (GL), Réseaux et Admin Système (RAS), Design et Multimédia (DM), Management des SI (MSI), Data Science (DS), DevOps et Sécurité (DEVOPS), Communication Numérique (CN) |
| **ECOPO** (4) | Gestion des Entreprises (GEIF), Management Commercial (MCM), Gestion des Affaires Publiques (GAP), Diversification Agro-Alimentaire (DDAI) |
| **KANSEBULA** (1) | Sciences de l'Homme et de la Société (SHS) |
| **THEOLOGICUM** (1) | Théologie (THEO) |

### 8.2 Utilisateurs de test (11)

Mot de passe pour tous : **`password`**

| Nom | Email | Matricule | Rôle | Filière |
|---|---|---|---|---|
| Administrateur Système | `admin@udbl-tfc.cd` | ADM-001 | Admin | — |
| Prof. Jean Kabongo | `cp.gl@udbl-tfc.cd` | CP-001 | Chef de département | Génie Logiciel |
| Prof. Sylvie Kyungu | `cp.ras@udbl-tfc.cd` | CP-002 | Chef de département | Réseaux et Admin Sys. |
| Prof. Joseph Kalala | `cp.ecopo@udbl-tfc.cd` | CP-003 | Chef de département | Gestion Entreprises |
| Prof. Marie Lukusa | `prof1@udbl-tfc.cd` | ENS-001 | Enseignant | Génie Logiciel |
| Prof. Patrick Mbuyi | `prof2@udbl-tfc.cd` | ENS-002 | Enseignant | Génie Logiciel |
| Prof. Claude Ngoy | `prof3@udbl-tfc.cd` | ENS-003 | Enseignant | Réseaux |
| David Mulongo | `etudiant1@udbl-tfc.cd` | ETU-001 | Etudiant | Génie Logiciel |
| Grace Katumba | `etudiant2@udbl-tfc.cd` | ETU-002 | Etudiant | Génie Logiciel |
| Paul Tshimanga | `etudiant3@udbl-tfc.cd` | ETU-003 | Etudiant | Réseaux |
| Esther Ilunga | `etudiant4@udbl-tfc.cd` | ETU-004 | Etudiant | Gestion Entreprises |

---

## 9. MIGRATIONS (20)

| # | Migration | Description |
|---|---|---|
| 1 | `create_departments_table` | Table des filières (name, code) |
| 2 | `create_users_table` | Table utilisateurs (+ matricule, department_id) |
| 3 | `create_cache_table` | Cache Laravel |
| 4 | `create_jobs_table` | File d'attente / jobs |
| 5 | `create_permission_tables` | Tables Spatie (roles, permissions, pivots) |
| 6 | `create_subjects_table` | Table des sujets |
| 7 | `create_thesis_files_table` | Fichiers TFC |
| 8 | `create_ai_reports_table` | Rapports d'analyse IA |
| 9 | `create_notifications_table` | Notifications Laravel |
| 10 | `create_academic_years_table` | Années académiques |
| 11 | `create_system_settings_table` | Paramètres système |
| 12 | `create_activity_logs_table` | Journal d'activité |
| 13 | `add_is_blocked_to_users_table` | Champ blocage utilisateur |
| 14 | `add_academic_year_id_to_subjects_table` | Lien sujet-année académique |
| 15 | `add_admin_permissions` | Permissions d'administration |
| 16 | `seed_system_settings` | Paramètres système par défaut |
| 17 | `add_defense_validated_to_subjects_table` | Champ autorisation soutenance |
| 18 | `add_structured_fields_to_subjects_table` | 10 champs structurés du wizard |
| 19 | `create_faculties_table` | Table des facultés + rattachement aux filières |
| 20 | `add_defense_details_to_subjects_table` | Date et salle de soutenance |

---

## 10. ROUTES (60)

### Routes publiques
| Méthode | URI | Description |
|---|---|---|
| GET | `/` | Page d'accueil |
| GET | `/archives` | Archives publiques |
| GET | `/archives/{id}/download` | Télécharger un fichier final |

### Routes authentifiées
| Méthode | URI | Middleware | Description |
|---|---|---|---|
| GET | `/dashboard` | auth, verified | Dashboard (rôle dynamique) |
| GET | `/profile` | auth | Consultation du profil (lecture seule) |

### Routes Etudiant
| Méthode | URI | Description |
|---|---|---|
| GET | `/subjects/create` | Formulaire wizard 5 étapes |
| POST | `/subjects` | Soumettre un sujet |
| POST | `/thesis/upload` | Déposer un PDF |

### Routes Chef de Filière
| Méthode | URI | Description |
|---|---|---|
| POST | `/subjects/{id}/validate` | Valider un sujet |
| POST | `/subjects/{id}/reject` | Rejeter un sujet |
| PATCH | `/subjects/{id}/schedule-defense` | Planifier date/salle de soutenance |

### Routes Enseignant
| Méthode | URI | Description |
|---|---|---|
| POST | `/subjects/{id}/authorize-defense` | Autoriser la soutenance |
| DELETE | `/subjects/{id}/authorize-defense` | Retirer le Feu Vert (si version finale non déposée, motif obligatoire) |

### Routes communes (auth)
| Méthode | URI | Description |
|---|---|---|
| GET | `/subjects` | Liste des sujets (filtrée par rôle) |
| GET | `/subjects/export` | Export CSV (Admin + CP uniquement) |
| GET | `/subjects/{id}` | Détail d'un sujet |
| GET | `/thesis/{id}/download` | Télécharger un fichier TFC |
| GET | `/notifications` | Liste des notifications |
| POST | `/notifications/{id}/read` | Marquer notification comme lue |
| POST | `/notifications/mark-all-read` | Tout marquer comme lu |
| DELETE | `/notifications/{id}` | Supprimer une notification |
| DELETE | `/notifications` | Supprimer toutes les notifications |

### Routes Admin (préfixe `/admin`)
| Méthode | URI | Description |
|---|---|---|
| GET | `/admin/users` | Liste utilisateurs |
| GET | `/admin/users/create` | Créer utilisateur |
| POST | `/admin/users` | Enregistrer utilisateur |
| GET | `/admin/users/{id}/edit` | Modifier utilisateur |
| PUT | `/admin/users/{id}` | Mettre à jour utilisateur |
| DELETE | `/admin/users/{id}` | Supprimer utilisateur |
| PATCH | `/admin/users/{id}/toggle-block` | Bloquer/débloquer |
| PATCH | `/admin/users/{id}/reset-password` | Réinitialiser mot de passe |
| GET | `/admin/faculties` | Liste facultés |
| GET | `/admin/faculties/create` | Créer faculté |
| POST | `/admin/faculties` | Enregistrer faculté |
| GET | `/admin/faculties/{id}/edit` | Modifier faculté |
| PUT | `/admin/faculties/{id}` | Mettre à jour faculté |
| DELETE | `/admin/faculties/{id}` | Supprimer faculté |
| GET | `/admin/departments` | Liste filières |
| GET | `/admin/departments/create` | Créer filière |
| POST | `/admin/departments` | Enregistrer filière |
| GET | `/admin/departments/{id}/edit` | Modifier filière |
| PUT | `/admin/departments/{id}` | Mettre à jour filière |
| DELETE | `/admin/departments/{id}` | Supprimer filière |
| GET | `/admin/academic-years` | Années académiques |
| GET | `/admin/academic-years/create` | Créer année |
| POST | `/admin/academic-years` | Enregistrer année |
| PATCH | `/admin/academic-years/{id}/set-current` | Définir courante |
| PATCH | `/admin/academic-years/{id}/close` | Clôturer |
| DELETE | `/admin/academic-years/{id}` | Supprimer année |
| GET | `/admin/settings` | Paramètres système |
| PUT | `/admin/settings` | Mettre à jour paramètres |
| GET | `/admin/logs` | Journal d'activité |

### Routes Auth (12 routes Breeze)
Login, forgot-password, reset-password, verify-email, confirm-password, logout (register désactivé).

---

## 11. COMPOSANTS BLADE (15)

| Composant | Utilisation |
|---|---|
| `<x-icon>` | 43 icônes Heroicons SVG inline |
| `<x-breadcrumb>` | Fil d'Ariane sur toutes les pages admin |
| `<x-modal>` | Fenêtres modales |
| `<x-primary-button>` | Boutons principaux |
| `<x-danger-button>` | Boutons de suppression |
| `<x-secondary-button>` | Boutons secondaires |
| `<x-text-input>` | Champs de saisie |
| `<x-input-label>` | Labels de formulaire |
| `<x-input-error>` | Messages d'erreur de validation |
| `<x-dropdown>` | Menus déroulants |
| `<x-dropdown-link>` | Liens dans les dropdowns |
| `<x-nav-link>` | Liens de navigation |
| `<x-responsive-nav-link>` | Liens nav mobile |
| `<x-auth-session-status>` | Messages de statut |
| `<x-application-logo>` | Logo de l'application |

---

## 12. PAGES D'ERREUR

4 pages d'erreur personnalisées en français :
- **403** — Accès interdit
- **404** — Page non trouvée
- **419** — Session expirée
- **500** — Erreur serveur

---

## 13. INTERFACE UTILISATEUR

- **Langue** : 100% français (locale `fr`, fallback `fr`)
- **Fuseau horaire** : Africa/Lubumbashi
- **Design** : Tailwind CSS avec palette bleue/indigo
- **Icônes** : 43 Heroicons SVG inline via `<x-icon>`
- **Graphiques** : Chart.js (doughnut + barres) sur le dashboard admin
- **Interactivité** : Alpine.js (wizard, toggles mot de passe, filtres, modales)
- **Responsive** : Navigation mobile avec menu hamburger

---

## 14. SECURITE

| Mesure | Implémentation |
|---|---|
| Authentification | Laravel Breeze (bcrypt, 12 rounds) |
| Rôles & permissions | Spatie Laravel Permission v7 |
| Middleware global | `CheckUserBlocked` — déconnexion automatique des comptes bloqués |
| Protection CSRF | Token `@csrf` sur tous les formulaires |
| Validation serveur | Validation Laravel sur tous les formulaires |
| Accès par rôle | Middleware `role:` sur les groupes de routes |
| Accès aux données | Vérification `department_id` / `student_id` / `teacher_id` dans les contrôleurs |
| Upload sécurisé | Validation MIME (PDF uniquement), taille max 20 Mo |
| Mot de passe admin | Affiché de manière éphémère dans un bandeau séparé (non dans l'URL) |
| Sessions | Stockées en base de données |

---

## 15. CONFIGURATION ENVIRONNEMENT (.env)

```env
# Application
APP_NAME="UDBL TFC Manager"
APP_ENV=local
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

# Base de données
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=udbl_tfc_manager
DB_USERNAME=postgres
DB_PASSWORD=

# Sessions & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (mode log en dev)
MAIL_MAILER=log

# Détection IA
AI_DETECTION_API_KEY=           # Cle API (optionnelle)
AI_DETECTION_API_URL=https://api.gptzero.me/v2/predict/text
```

---

## 16. INSTALLATION ET DEMARRAGE

```bash
# 1. Cloner le projet
git clone <repo> udbl-tfc-manager
cd udbl-tfc-manager

# 2. Installer les dépendances
composer install
npm install

# 3. Configuration
cp .env.example .env
php artisan key:generate

# 4. Base de données
# Configurer PostgreSQL dans .env (DB_CONNECTION=pgsql, etc.)
php artisan migrate --seed

# 5. Stockage
php artisan storage:link

# 6. Build frontend
npm run build

# 7. Démarrer le serveur
php artisan serve
# => http://127.0.0.1:8000

# Connexion admin : admin@udbl-tfc.cd / password
```

---

## 17. CE QUI RESTE A FAIRE (AMELIORATIONS FUTURES)

### Priorité moyenne

| # | Amélioration | Détail |
|---|---|---|
| 1 | **Configuration mail production** | Remplacer `MAIL_MAILER=log` par un SMTP réel (Mailtrap, Gmail, etc.) pour envoyer les notifications par email |
| 2 | **Base de données production** | Renforcer la stratégie PostgreSQL en production (sauvegardes, supervision, réplication selon besoins) |
| 3 | **Tests automatisés** | Ecrire des tests Feature pour les workflows métier (soumission, validation, upload, rôles) |
| 4 | **Modales de confirmation** | Ajouter des modales JavaScript avant les suppressions |
| 5 | **Filtres admin avancés** | Ajouter recherche/filtres sur filières, années académiques, logs |

### Priorité basse

| # | Amélioration | Détail |
|---|---|---|
| 6 | **Changement d'encadreur** | Permettre au CP de réassigner un encadreur après validation |
| 7 | **Historique des soumissions** | Garder un historique des sujets rejetés/resoumis |
| 8 | **Graphiques CP** | Ajouter des statistiques/graphiques au dashboard Chef de Filière |
| 9 | **Mode sombre** | Support du dark mode avec Tailwind |
| 10 | **Déploiement** | Ajouter Dockerfile et/ou configuration de déploiement |
| 11 | **Documentation utilisateur** | Guide d'utilisation avec captures d'écran |
| 12 | **Photo de profil** | Upload de photo dans le profil utilisateur |

---

## 18. RESUME FINAL

| Critère | Statut |
|---|---|
| Architecture Laravel | Complète et conforme |
| Système d'authentification | Fonctionnel (Breeze) |
| Rôles et permissions (4 rôles, 17 permissions) | Implémenté (Spatie) |
| Workflow sujets (soumission - validation - assignation - soutenance) | Fonctionnel |
| Wizard 5 étapes (soumission structurée) | Fonctionnel (Alpine.js) |
| Upload et analyse PDF | Fonctionnel (smalot/pdfparser + API IA) |
| Fournisseurs IA (GPTZero + simulation) | Configuré |
| 4 dashboards par rôle | Implémentés |
| Administration complète (CRUD users/facultés/filières/années/params/logs) | Fonctionnel |
| 6 notifications (database + mail) | Implémentées |
| Export CSV | Fonctionnel (Admin + CP) |
| Pages d'erreur FR | 4 pages (403, 404, 419, 500) |
| Heroicons | 43 icônes via composant `<x-icon>` |
| Breadcrumbs | Sur toutes les pages admin |
| Interface 100% français | Oui |
| Sécurité (middleware, blocage, CSRF, rôles) | En place |
| Build Vite | Fonctionnel |

**L'application est fonctionnellement complète et prête pour la démonstration.**

---

*Rapport généré le 12 février 2026 — UDBL TFC Manager v1.0*
