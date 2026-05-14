# Architecture Fonctionnelle — Plateforme de Gestion Académique (TFC Manager)

## 1. Résumé par rôle
- **Étudiant (Chercheur)**: inscription (faculté/filière), proposer sujets, postuler auprès d'un directeur, rédiger (éditeur en ligne / import), versions, répondre aux commentaires, suivre validation par `Chef de Département`, déposer version finale après `BAT`.
- **Chef de Département / Responsable de Filière**: revue des sujets de sa filière, approuver/rejeter/demander modification, affectation officielle étudiant↔directeur, consulter liste enseignants et charge, extraire statistiques d'avancement.
- **Directeur (Enseignant)**: accepter/decliner demandes d'encadrement, annoter/corriger/valider chapitres, lancer analyses IA/plagiat, communication (chat/rendez-vous), signer numériquement le `BAT`.
- **Doyen de Faculté**: supervision des filières de sa faculté, consulter répertoire enseignants/étudiants, arbitrage, générer rapports consolidés.
- **Administrateur Central**: créer/gérer 4 facultés & filières, gérer comptes (doyens/chefs/enseignants), maintenance (sauvegardes, sécurité, MAJ IA), paramétrer calendriers (deadlines, périodes soutenance).
- **Lecteur / Jury (accès temporaire)**: consulter document final validé avant soutenance, encoder cotes/rapports.

## 2. Règles métier essentielles
- Hiérarchie: `Faculty` → `Filiere` → utilisateurs. Portée d'accès limitée: Chef = filière, Doyen = faculté, Admin = global.
- Flux de validation: `proposé` → `en_revue` → `approuvé`|`rejeté`|`à_modifier` → `affecté` → `officiel`.
- `BAT` (Bon à Tirer): signé numériquement par le `Directeur`; dépôt final autorisé seulement après signature.
- Versioning obligatoire: chaque `Chapter` conserve `ChapterVersion`.
- Commentaires: threads, statut `résolu`/`non_résolu`.
- Audit/logs: journaliser validations, signatures, uploads, actions admin.
- IA / Plagiat: traitement asynchrone (queue jobs) sur nouvelles versions.

## 3. Modèle de données (entités clés & relations)
- `Faculty` (id, name)
- `Filiere` (id, name, faculty_id) — belongsTo `Faculty`
- `User` (id, name, email, role, faculty_id, filiere_id, meta)
- `Subject` (id, title, abstract, student_id, filiere_id, status, created_at)
- `Thesis` (id, subject_id, student_id, director_id, status, bat_signed_at, final_file_id)
- `Chapter` (id, thesis_id, title, position)
- `ChapterVersion` (id, chapter_id, file_ref, content_snapshot, created_by, created_at, checksum)
- `Comment` (id, version_id, author_id, content, parent_id, resolved)
- `Milestone` (id, thesis_id, name, due_date, status)
- `AiReport` / `PlagiarismReport` (id, version_id, score, details, processed_at)
- `AuditLog` (id, user_id, action, target_type, target_id, meta, created_at)
- `Notification`, `CalendarEvent`, `BackupRecord`

Relations importantes:
- `Filiere` 1—n `User`, `Subject`.
- `Subject` → `Thesis` (1—1 après affectation).
- `Thesis` 1—n `Chapter`; `Chapter` 1—n `ChapterVersion`.
- `ChapterVersion` 1—n `Comment`; `ChapterVersion` 1—n `AiReport`.

## 4. Workflows & états (exemples)
- Sujet: `proposé` → `en_revue` → (`approuvé` | `rejeté` | `à_modifier`) → `affecté` → `officiel`.
- Thèse (cycle): `brouillon` → `soumis` → `en_validation` → (`retour_revision`) → `validé` → `BAT_signé` → `déposé_final` → `archivé`.
- Version/chapitre: `en_rédaction` → `soumis_pour_review` → (`reviser` → `accepté`) → version historique.
- Milestone: `ouvert` → `en_cours` → (`en_retard`) → `complété`.
- BAT: signature numérique déclenche changement `BAT_signé` + audit + notif student.

## 5. Matrice des permissions (exemples — noms utilisables avec `spatie/laravel-permission`)
- **Permission groups** (section.action): `subjects.*`, `theses.*`, `chapters.*`, `comments.*`, `users.*`, `reports.*`, `ai.*`, `backups.*`, `calendar.*`, `jury.*`.

Permissions clés par rôle:
- **Étudiant**: `subjects.create`, `subjects.view_own`, `subjects.apply`, `theses.create`, `chapters.create`, `chapters.import`, `chapter_versions.view_own`, `comments.create`, `theses.submit`, `theses.finalize` (après `BAT`).
- **Chef de Département**: `subjects.review`, `subjects.approve`, `subjects.reject`, `subjects.request_changes`, `subjects.assign`, `statistics.view.filiere`, `reports.generate.filiere`.
- **Directeur**: `supervision.accept`, `chapters.annotate`, `chapters.update`, `ai.run`, `plagiarism.run`, `theses.bat.sign`, `comments.reply`, `theses.view_assigned`.
- **Doyen**: `faculty.overview`, `reports.generate.faculty`, `arbitrage.access`.
- **Administrateur Central**: `faculties.manage`, `filieres.manage`, `users.manage`, `backups.manage`, `calendar.manage`, `system.settings`.
- **Jury / Lecteur**: `jury.read`, `jury.evaluate` (accès temporaire scoppé à une thèse).

Notes: préférer rôles + permissions fines + scope (policies) basés sur `faculty_id`/`filiere_id`.

## 6. API suggérée (endpoints REST — auth via Sanctum/JWT + middleware RBAC)
- `POST /api/subjects` — créer sujet (`subjects.create`).
- `GET /api/subjects` — lister (filtrage par filière/faculté/statut) (`subjects.view`).
- `POST /api/subjects/{id}/apply` — postuler auprès d'un directeur (`subjects.apply`).
- `POST /api/subjects/{id}/review` — action Chef (approve/reject/request_changes) (`subjects.review`).
- `POST /api/subjects/{id}/assign` — affectation officielle (`subjects.assign`).
- `POST /api/theses` — créer thèse (lié à sujet) (`theses.create`).
- `POST /api/theses/{id}/chapters` — ajouter chapitre (`chapters.create`).
- `POST /api/chapters/{id}/versions` — uploader/versionner (`chapter_versions.create`).
- `GET /api/chapters/{id}/versions` — historique (`chapter_versions.view`).
- `POST /api/comments` — commenter / répondre (`comments.create`).
- `POST /api/theses/{id}/bat/sign` — signature BAT (digital) (`theses.bat.sign`).
- `POST /api/theses/{id}/finalize` — dépôt final (`theses.finalize`).
- `GET /api/reports/faculty/{id}` — rapports consolidés (`reports.generate`).
- `POST /api/ai/scan` — lancer analyse IA / plagiat (asynchrone) (`ai.run`).

Middleware recommandés:
- Auth + RBAC (permission), Policy check (scope filière/faculté), Rate limiting, Validation payload, File scan.

## 7. Recommandations d'implémentation (Laravel)
- Utiliser `spatie/laravel-permission` pour roles/permissions; stocker scope (`faculty_id`, `filiere_id`) sur `User` et vérifier via `Policy` (`SubjectPolicy`, `ThesisPolicy`, `ChapterPolicy`).
- Policies: appliquer règles fines (ex: Chef ne peut approuver que sujets de sa filière).
- Versioning: `chapter_versions` + stockage fichier sur `storage` (S3 recommandé) + checksum + retention policy.
- IA/Plagiat: jobs en queue (`dispatch`) ; notification asynchrone ; stocker `AiReport` lié à `ChapterVersion`.
- Commentaires temps réel: `Laravel Echo` + Pusher / Redis + events `CommentAdded`.
- BAT: signature numérique (Signer metadata: signer_id, signature_hash, signed_pdf_ref, horodatage) ; audit + immutabilité du fichier final.
- Audit & sauvegarde: table `audit_logs` ; backups automatisés chiffrés, tests de restauration.
- Sécurité: validation uploads, scan antivirus, quotas, CORS/CSRF, rate-limits sur endpoints critiques, stockage chiffré pour fichiers sensibles.
- Tests: factories, tests d'intégration pour workflows (soumission → validation → BAT → dépôt final).
- UI/UX: statuts visibles, timeline par thèse, notifications claires, historique de versions.

## 8. Observations opérationnelles
- Contrainte de confidentialité: accès jury/lecteur temporaires, limiter téléchargement avant BAT sauf si autorisé.
- Charge IA: planifier quotas et tickets pour analyses importantes (batching, priorisation).
- Politique de rétention: conserver versions majeures + x dernières versions pour stockage.

## 9. Prochaines livrables possibles
- Matrice permissions au format CSV/Markdown (exportable).
- Migrations Laravel pour entités `Subject`, `Thesis`, `Chapter`, `ChapterVersion`, `AiReport`.
- Scaffolding: routes API, contrôleurs, policies, tests d'intégration pour le flux sujet → thèse → BAT.

---

*Généré automatiquement.*
