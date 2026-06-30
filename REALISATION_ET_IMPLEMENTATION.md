# CHAPITRE : RÉALISATION ET IMPLÉMENTATION DE LA PLATEFORME UDBL-TFC-MANAGER

Ce document présente les détails techniques, l'architecture, les choix technologiques et les étapes de réalisation du système **UDBL TFC Manager** (Système intégré de régulation, d'audit d'intégrité et d'archivage des Travaux de Fin de Cycle de l'Université Don Bosco de Lubumbashi).

---

## 1. Architecture du Système

Le système est conçu sur une architecture MVC (Modèle-Vue-Contrôleur) robuste, permettant de séparer clairement la logique métier, l'accès aux données et l'interface utilisateur.

### 1.1 Choix Technologiques
- **Backend Framework** : Laravel 11 (PHP 8.2+), choisi pour son écosystème riche, sa sécurité native et sa capacité à gérer des applications complexes.
- **Frontend** : Blade Templates couplé à **Tailwind CSS** pour un design réactif, moderne et épuré. L'interactivité est assurée par Alpine.js.
- **Base de données** : MySQL / PostgreSQL gérée par l'ORM Eloquent.
- **Services NLP & IA** : Intégration d'outils d'analyse sémantique et de détection de similitudes (Anti-plagiat).
- **Génération de fichiers** : Conversion automatique de documents (Word vers PDF) et extraction de textes via des librairies spécialisées (`smalot/pdfparser`).

### 1.2 Structure Modulaire
L'application est divisée en plusieurs modules fonctionnels clés :
- **Module d'Authentification & Autorisation** : Gestion des accès basée sur les rôles (RBAC) via le package `spatie/laravel-permission` (Administrateur, Étudiant, Enseignant, Chef de département).
- **Module de Gestion Académique** : Gestion des années académiques, facultés, et filières.
- **Module d'Instruction des Sujets** : Processus de validation des TFC et Mémoires (Proposition -> Validation -> Assignation de Directeur).
- **Module d'Audit et Anti-plagiat** : Services `SimilarityDetectionService` et `AiDetectionService` permettant le calcul du score TF-IDF et la vérification de l'intégrité intellectuelle.
- **Module d'Archivage** : Dépôt numérique interopérable compatible avec la norme OAI-PMH (Dublin Core) pour le moissonnage institutionnel.

---

## 2. Implémentation des Fonctionnalités Clés

### 2.1 Gestion des Rôles et des Flux de Travail (Workflows)
Chaque utilisateur interagit avec la plateforme selon un périmètre strict :
- **L'Étudiant** soumet son sujet, dépose les différentes versions de ses chapitres, et visualise l'état de validation.
- **Le Chef de Département** valide les sujets, détecte les doublons grâce à l'algorithme de similarité, et assigne les directeurs.
- **L'Enseignant (Directeur)** examine le travail, soumet des commentaires et approuve les versions finales.
- **L'Administrateur** a une vision globale du système (dashboard, logs d'activités, gestion des comptes bloqués, paramétrages globaux).

### 2.2 Algorithme de Détection de Similarité (Anti-Plagiat)
Le cœur de la régulation académique repose sur le service de détection :
- **Vectorisation des textes** : Le texte des documents soumis est nettoyé et vectorisé.
- **Analyse TF-IDF (Term Frequency-Inverse Document Frequency)** : Évaluation de l'importance d'un mot dans le document relativement à l'ensemble des travaux archivés.
- **Comparaison Sémantique** : Calcul du score de similarité cosinus entre le nouveau document et les archives existantes. Tout dépassement du seuil critique déclenche une alerte.

### 2.3 Archivage et Norme OAI-PMH
Pour assurer la pérennité et la visibilité des travaux scientifiques de l'UDBL, un point de terminaison OAI-PMH a été implémenté.
- Les données des TFC et Mémoires validés sont formatées en **XML Dublin Core**.
- Cela permet l'intégration automatique des recherches des étudiants dans des répertoires internationaux ou régionaux.

---

## 3. Sécurité et Audit

La sécurisation des données est une priorité dans l'implémentation :
- **Traçabilité totale** : Un système de logs (`ActivityLog`) enregistre chaque action sensible (création, modification, blocage, suppression) avec l'identité de l'opérateur et l'horodatage.
- **Protection des Fichiers** : Les documents téléchargés par les étudiants sont stockés dans des répertoires sécurisés du système de fichiers (`storage/app/public/archives`). Les accès sont contrôlés via des contrôleurs (`ArchiveController::download`).
- **Prévention des attaques** : Utilisation des protections natives de Laravel (CSRF, XSS, requêtes préparées contre les injections SQL, et Rate Limiting).

---

## 4. Expérience Utilisateur (UI/UX)

L'interface graphique a été conçue pour refléter l'exigence d'une institution académique tout en restant intuitive :
- **Glassmorphism et Modernité** : Utilisation d'effets de flou (`backdrop-blur`), de dégradés subtils et de micro-animations pour une navigation fluide.
- **Dashboard Dynamique** : Tableaux de bord intégrant la librairie `Chart.js` pour visualiser en temps réel l'évolution des sujets (Sujets validés, en instruction, rejetés) et la répartition par filière.
- **Responsive Design** : Le système s'adapte automatiquement aux formats mobiles, tablettes et ordinateurs de bureau grâce aux utilitaires Tailwind CSS.

---

## 5. Déploiement et Maintenance

- **Automatisation** : Des commandes Artisan personnalisées et des Jobs en arrière-plan (`Queue`) sont utilisés pour le traitement de tâches lourdes (comme la conversion PDF ou le calcul sémantique approfondi).
- **Mises à jour** : L'architecture découplée garantit que les futures mises à jour logicielles (évolution des règles académiques, ajout de nouvelles facultés) peuvent être effectuées sans interrompre le fonctionnement global de l'application.

---

**Conclusion** : 
L'implémentation de la plateforme **UDBL-TFC-MANAGER** dote l'Université Don Bosco de Lubumbashi d'un outil souverain, fiable et moderne. Il fluidifie la gestion administrative, garantit l'intégrité scientifique des recherches et ouvre l'université sur le réseau académique mondial via des standards d'archivage reconnus.
