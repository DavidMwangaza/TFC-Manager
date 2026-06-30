# 🎓 TFC Manager — Plateforme de Gestion des Travaux de Fin de Cycle

**TFC Manager** est une application web de gestion académique développée pour l'**Université du Bassin du Lac (UDBL)**. Elle permet la soumission, le suivi, l'encadrement et l'archivage des Travaux de Fin de Cycle (TFC) et Mémoires, avec un système de rôles hiérarchique, une détection IA intégrée et un protocole d'intégrité scientifique.

---

## 📸 Aperçu

| Tableau de bord Étudiant | Fiche de Sujet | Tableau de bord Enseignant |
|---|---|---|
| Suivi des jalons, dépôt PDF | Détail structuré, historique | Corrections, Feu Vert, Score IA |

---

## ✨ Fonctionnalités Principales

### 🧑‍🎓 Étudiant
- Soumission de sujet via un formulaire structuré en 5 étapes (contexte, problématique, hypothèses, objectifs, état de l'art)
- Dépôt de fichiers PDF (version Jury et version Finale)
- Soumission de livrables pour les jalons assignés par le directeur
- Suivi de l'avancement via un tableau de bord personnalisé
- Notifications en temps réel (validation, rejet, jalons assignés)

### 👨‍🏫 Enseignant / Directeur de TFC
- Tableau de bord listant les étudiants supervisés et les corrections en attente
- Création de jalons (milestones) avec échéances et SLA de correction
- Validation / Rejet de jalons avec commentaires
- **Analyse IA à la demande** (déclenchement manuel, score visible uniquement par le directeur)
- Octroi du **Feu Vert** (autorisation de soutenance) — nécessite que tous les jalons soient validés
- Signature numérique du **BAT** (Bon à Tirer)
- Révocation du Feu Vert (si version finale non encore déposée)

### 📋 Chef de Département
- Validation ou rejet des sujets soumis par les étudiants
- Assignation d'un enseignant directeur (avec contrôle du quota)
- Planification de la soutenance (date, salle, jury)
- Vue statistique sur les sujets de la filière
- Export CSV des sujets

### 🏛️ Doyen de Faculté
- Vue statistique globale sur toutes les filières de la faculté
- Suivi des taux de validation et de progression

### 💰 Appariteur
- Validation financière de l'étudiant (pré-requis pour le dépôt final)

### 🔧 Administrateur
- Gestion complète des utilisateurs (CRUD, blocage, réinitialisation de mot de passe)
- Gestion des facultés, filières et années académiques
- Paramètres système (quota max d'étudiants par enseignant, etc.)
- Journal d'activité complet (audit trail)

### 📚 Archives Publiques
- Consultation des travaux défendus et archivés
- Recherche multi-critères (titre, auteur, filière, année)
- Téléchargement des manuscrits
- Point d'accès **OAI-PMH** pour l'interopérabilité avec les dépôts institutionnels

### 🤖 Détection IA & Anti-Plagiat
- Intégration de l'API **GPTZero** pour la détection de contenu généré par IA
- Score de détection IA (`ai_score`) et score de similarité (`similarity_score`)
- Mode simulation disponible en développement (si la clé API n'est pas configurée)
- Analyse NLP TF-IDF pour la détection de similarité entre sujets lors de la soumission

---

## 🏗️ Stack Technique

| Composant | Technologie |
|---|---|
| **Backend** | PHP 8.2+ / Laravel 12 |
| **Frontend** | Blade + Alpine.js |
| **CSS** | Tailwind CSS 3 |
| **Bundler** | Vite |
| **Base de données** | SQLite (dev) / PostgreSQL (prod) |
| **Rôles & Permissions** | [spatie/laravel-permission](https://github.com/spatie/laravel-permission) v7 |
| **PDF Parser** | [smalot/pdfparser](https://github.com/smalot/pdfparser) |
| **NLP / ML** | [php-ai/php-ml](https://github.com/php-ai/php-ml) (TF-IDF) |
| **Détection IA** | API GPTZero (v2) |
| **Tests** | PHPUnit 11 |

---

## 📂 Architecture du Projet

```
app/
├── Console/Commands/      # Commandes Artisan (CheckMilestoneSla)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         # UserController, FacultyController, DepartmentController, etc.
│   │   ├── Api/           # SubjectApiController (API REST)
│   │   ├── AppariteurController.php
│   │   ├── ArchiveController.php
│   │   ├── DashboardController.php
│   │   ├── FeedbackController.php
│   │   ├── MilestoneController.php
│   │   ├── SubjectController.php
│   │   └── ThesisFileController.php
│   └── Middleware/        # PreventArchivedSubjectModification
├── Jobs/                  # AnalyzeThesisFileAi, AnalyzeSubjectSimilarity, etc.
├── Models/                # User, Subject, Milestone, ThesisFile, AiReport, etc.
├── Notifications/         # 14 classes de notification (MilestoneAssigned, DefenseAuthorized, etc.)
└── Services/              # AiDetectionService, SimilarityDetectionService, SemanticSearchService

database/
├── migrations/            # 30+ migrations
└── seeders/               # RolesAndPermissionsSeeder, UserSeeder, TestDataSeeder

resources/views/
├── admin/                 # Dashboard, utilisateurs, facultés, filières, paramètres
├── appariteur/            # Dashboard Appariteur
├── cp/                    # Dashboard Chef de département
├── doyen/                 # Dashboard Doyen
├── student/               # Dashboard Étudiant
├── teacher/               # Dashboard Enseignant
├── subjects/              # CRUD sujets, partials (milestones)
├── archives/              # Consultation publique
└── components/            # Composants Blade réutilisables

tests/Feature/             # Tests fonctionnels (42 tests, 117 assertions)
```

---

## 🚀 Installation

### Prérequis

- **PHP** ≥ 8.2 avec les extensions `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`
- **Composer** ≥ 2.x
- **Node.js** ≥ 18.x et **npm**
- **SQLite** (développement) ou **PostgreSQL** (production)

### Installation rapide

```bash
# 1. Cloner le dépôt
git clone https://github.com/DavidMwangaza/TFC-Manager.git
cd TFC-Manager

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier d'environnement
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Installer les dépendances frontend
npm install

# 6. Compiler les assets
npm run build

# 7. Créer la base de données et peupler les données de test
php artisan migrate:fresh --seed

# 8. Créer le lien de stockage
php artisan storage:link

# 9. Lancer le serveur de développement
php artisan serve
```

### Script de développement tout-en-un

```bash
composer dev
```

Cette commande lance simultanément :
- Le serveur Laravel (`php artisan serve`)
- Le worker de queue (`php artisan queue:listen`)
- Les logs en temps réel (`php artisan pail`)
- Le serveur Vite (`npm run dev`)

---

## 🔑 Comptes de Test

Après avoir exécuté `php artisan migrate:fresh --seed`, les comptes suivants sont disponibles.
**Mot de passe universel : `password`**

| Rôle | Email | Description |
|---|---|---|
| 🔧 Admin | `admin@udbl-tfc.cd` | Accès total à l'administration |
| 📋 Chef Dept GL | `cp.gl@udbl-tfc.cd` | Chef de département Génie Logiciel |
| 📋 Chef Dept RAS | `cp.ras@udbl-tfc.cd` | Chef de département Réseaux & Systèmes |
| 📋 Chef Dept ECOPO | `cp.ecopo@udbl-tfc.cd` | Chef de département Économie Politique |
| 👨‍🏫 Enseignant 1 | `prof1@udbl-tfc.cd` | Directeur de TFC |
| 👨‍🏫 Enseignant 2 | `prof2@udbl-tfc.cd` | Directeur de TFC |
| 👨‍🏫 Enseignant 3 | `prof3@udbl-tfc.cd` | Directeur de TFC |
| 🎓 Étudiant 1 | `etudiant1@udbl-tfc.cd` | Sujet validé avec jalons |
| 🎓 Étudiant 2 | `etudiant2@udbl-tfc.cd` | Sujet validé avec fichier TFC |
| 🎓 Étudiant 3–8 | `etudiant3@udbl-tfc.cd` … | Sujets en attente / rejetés |

---

## 🔄 Workflow Métier

```
┌─────────────┐    ┌──────────────┐    ┌──────────────────┐    ┌─────────────┐
│  ÉTUDIANT   │───▶│  CHEF DEPT   │───▶│   ENSEIGNANT     │───▶│   DÉFENSE   │
│  Soumet     │    │  Valide +    │    │   (Directeur)    │    │             │
│  le sujet   │    │  Assigne     │    │                  │    │             │
└─────────────┘    │  Enseignant  │    │  Crée jalons     │    └──────┬──────┘
                   └──────────────┘    │  Valide jalons   │           │
                                       │  Analyse IA      │           │
                                       │  Feu Vert ✅      │           │
                                       └──────────────────┘           │
                                                                      ▼
┌─────────────┐    ┌──────────────┐    ┌──────────────────┐    ┌─────────────┐
│  APPARITEUR │───▶│  ÉTUDIANT    │───▶│   ENSEIGNANT     │───▶│  ARCHIVES   │
│  Valide     │    │  Dépose      │    │   Signe BAT      │    │  Publiques  │
│  finances   │    │  version     │    │   📝              │    │  📚         │
│  💰         │    │  finale      │    │                  │    │             │
└─────────────┘    └──────────────┘    └──────────────────┘    └─────────────┘
```

### Cycle de vie d'un sujet

1. **Soumission** — L'étudiant soumet sa fiche de proposition (statut `pending`)
2. **Audit NLP** — Le système calcule automatiquement la similarité avec les sujets existants (TF-IDF)
3. **Validation** — Le Chef de département valide le sujet et assigne un directeur (statut `validated`)
4. **Encadrement** — Le directeur crée des jalons et accompagne l'étudiant
5. **Soumission de jalons** — L'étudiant dépose ses livrables PDF pour chaque jalon
6. **Analyse IA** — Le directeur peut demander manuellement une analyse IA sur chaque fichier
7. **Feu Vert** — Quand tous les jalons sont validés, le directeur accorde l'autorisation de soutenance
8. **Validation financière** — L'Appariteur confirme que l'étudiant est en règle
9. **Dépôt final** — L'étudiant dépose sa version finale
10. **Signature BAT** — Le directeur signe numériquement le Bon à Tirer
11. **Archivage** — Le sujet est archivé et accessible publiquement

---

## 🧪 Tests

L'application dispose de **42 tests fonctionnels** couvrant les scénarios critiques :

```bash
# Exécuter tous les tests
php artisan test

# Exécuter un test spécifique
php artisan test --filter="test_etudiant_peut_soumettre_un_jalon"
```

### Suites de tests

| Suite | Fichier | Couverture |
|---|---|---|
| Jalons (Milestones) | `MilestoneTest.php` | Création, soumission, validation de jalons |
| Autorisation de soutenance | `TeacherDefenseAuthorizationTest.php` | Feu Vert, révocation, contrôle jalons |
| Signature BAT | `BatSigningTest.php` | Workflow de signature numérique |
| Chapitres | `ChapterWorkflowTest.php` | Gestion de chapitres et versions |
| Notifications | `NotificationManagementTest.php` | Envoi, lecture, suppression |
| Profil | `ProfileTest.php` | Mise à jour du profil utilisateur |
| Authentification | `Auth/` | Login, inscription, réinitialisation |

---

## ⚙️ Configuration

### Variables d'environnement clés

```env
# Base de données (PostgreSQL recommandé en production)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=udbl_tfc_manager
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe

# Détection IA (laisser vide pour le mode simulation)
AI_DETECTION_PROVIDER=gptzero
AI_DETECTION_API_KEY=votre_clé_gptzero
AI_DETECTION_API_URL=https://api.gptzero.me/v2/predict/text
```

### Paramètres système (modifiables depuis l'interface Admin)

| Paramètre | Valeur par défaut | Description |
|---|---|---|
| `max_students_per_teacher` | 5 | Quota maximum d'étudiants par enseignant |

---

## 🔐 Rôles et Permissions

| Rôle | Permissions |
|---|---|
| **Admin** | Toutes les permissions |
| **Chef de département** | `subjects.view`, `subjects.validate`, `subjects.reject`, `subjects.assign-teacher`, `thesis.download`, `thesis.view-reports` |
| **Enseignant** | `subjects.view`, `thesis.download`, `thesis.view-reports`, `thesis.validate-defense` |
| **Etudiant** | `subjects.create`, `subjects.view`, `thesis.upload`, `thesis.final-deposit` |
| **Doyen** | `subjects.view`, `statistics.faculty`, `thesis.view-reports` |
| **Appariteur** | `subjects.view`, `students.validate-financial` |

---

## 📡 API REST

Une API REST est disponible pour l'intégration avec des systèmes tiers :

| Méthode | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/subjects` | Lister les sujets (filtrable) |
| `GET` | `/api/v1/subjects/{id}` | Détail d'un sujet |

---

## 🛠️ Commandes Artisan personnalisées

```bash
# Vérifier les SLA des jalons et envoyer les rappels
php artisan milestones:check-sla
```

---

## 📄 Licence

Ce projet est développé dans le cadre d'un TFC à lUDBL.

---

## 👨‍💻 Auteur

**David Mwangaza** — Étudiant en Génie Logiciel, UDBL

Dépôt GitHub : [https://github.com/DavidMwangaza/TFC-Manager](https://github.com/DavidMwangaza/TFC-Manager)
