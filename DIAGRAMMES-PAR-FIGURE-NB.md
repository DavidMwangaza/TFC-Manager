# Diagrammes en Noir et Blanc - Version par Figure

Ce document presente les diagrammes figure par figure, dans l'ordre de la liste des figures.
Tous les diagrammes sont en noir et blanc.

## Figure 2 - Diagramme de cas d'utilisation global

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph TB
    A[Visiteur Public] --> U1[Consulter archives]
    A --> U2[Rechercher]
    B[Etudiant] --> U3[Soumettre sujet]
    B --> U4[Deposer fichier]
    C[Chef de Departement] --> U5[Valider ou rejeter]
    C --> U6[Assigner enseignant]
    C --> U10[Planifier soutenance]
    D[Enseignant] --> U7[Autoriser soutenance]
    E[Administrateur] --> U8[Gerer utilisateurs]
    E --> U9[Gerer structure academique]
```

## Figure 3 - Cas d'utilisation Etudiant

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    E[Etudiant] --> A[S inscrire]
    E --> B[Se connecter]
    E --> C[Soumettre sujet]
    E --> D[Deposer version jury]
    E --> F[Consulter rapport IA]
    E --> G[Deposer version finale]
    E --> H[Consulter notifications]
    E --> I[Consulter planification]
```

## Figure 4 - Cas d'utilisation Chef de Departement

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    C[Chef de Departement] --> A[Consulter sujets en attente]
    C --> B[Valider sujet]
    C --> D[Rejeter sujet]
    C --> E[Assigner encadreur]
    C --> F[Suivre TFC]
    C --> G[Exporter CSV]
    C --> H[Planifier soutenance]
```

## Figure 5 - Cas d'utilisation Enseignant

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    T[Enseignant] --> A[Consulter sujets encadres]
    T --> B[Telecharger fichiers]
    T --> C[Consulter rapport IA]
    T --> D[Consulter planification]
    T --> E[Autoriser soutenance]
```

## Figure 6 - Cas d'utilisation Administrateur

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Administrateur] --> B[Gerer utilisateurs]
    A --> C[Gerer facultes]
    A --> D[Gerer filieres]
    A --> E[Gerer annees académiques]
    A --> F[Configurer parametres]
    A --> G[Consulter journal]
```

## Figure 7 - Sequence Soumission de sujet

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
sequenceDiagram
    participant E as Etudiant
    participant C as SubjectController
    participant DB as Base de donnees
    participant S as Service Notification
    participant CP as Chef de Departement

    E->>C: Soumettre formulaire
    C->>DB: Verifier unicite
    C->>DB: Creer sujet en attente
    C->>S: Generer notification
    S->>CP: Envoyer notification
```

## Figure 8 - Sequence Validation ou rejet

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
sequenceDiagram
    participant CP as Chef de Departement
    participant C as SubjectController
    participant DB as Base de donnees
    participant S as Service Notification
    participant E as Etudiant
    participant T as Enseignant

    CP->>C: Valider + assigner enseignant
    C->>DB: Mettre a jour sujet valide
    C->>S: Notifier etudiant et enseignant
    S->>E: Sujet valide
    S->>T: Sujet assigne

    CP->>C: Rejeter avec motif
    C->>DB: Mettre a jour sujet rejete
    C->>S: Notifier etudiant
```

## Figure 9 - Sequence Depot de fichier avec analyse IA

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
sequenceDiagram
    participant E as Etudiant
    participant C as ThesisFileController
    participant F as Stockage
    participant AI as AiDetectionService
    participant API as API GPTZero
    participant DB as Base de donnees

    E->>C: Deposer PDF
    C->>F: Stocker fichier
    C->>DB: Inserer thesis_file
    C->>AI: Lancer analyse
    AI->>API: Envoyer texte
    API-->>AI: Retour scores
    AI->>DB: Inserer ai_report
```

## Figure 10 - Sequence Autorisation de soutenance

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
sequenceDiagram
    participant T as Enseignant
    participant C as SubjectController
    participant DB as Base de donnees
    participant S as Service Notification
    participant E as Etudiant
    participant CP as Chef de Departement

    T->>C: Autoriser soutenance
    C->>DB: defense_validated = true
    C->>S: Notifier etudiant
    S->>E: Autorisation accordee
    CP->>C: Planifier soutenance
    C->>DB: Enregistrer date et salle
```

## Figure 11 - Diagramme de classes

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
classDiagram
    Faculty --> Department
    Department --> User
    Department --> Subject
    AcademicYear --> Subject
    User --> Subject
    Subject --> ThesisFile
    ThesisFile --> AiReport
    User --> Notification
    User --> ActivityLog
```

## Figure 12 - Modele relationnel de la base de donnees

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
erDiagram
    FACULTIES ||--o{ DEPARTMENTS : contient
    DEPARTMENTS ||--o{ USERS : regroupe
    DEPARTMENTS ||--o{ SUBJECTS : possede
    USERS ||--o{ SUBJECTS : soumet
    USERS ||--o{ SUBJECTS : encadre
    ACADEMIC_YEARS ||--o{ SUBJECTS : organise
    SUBJECTS ||--o{ THESIS_FILES : contient
    THESIS_FILES ||--o{ AI_REPORTS : produit
```

## Figure 13 - Diagramme d'activites du processus complet

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph TD
    A[Soumission] --> B{Validation Chef?}
    B -->|Non| C[Rejet et motif]
    C --> A
    B -->|Oui| D[Assignation enseignant]
    D --> E[Depot version jury]
    E --> F[Analyse IA]
    F --> G[Autorisation soutenance]
    G --> H[Planification soutenance]
    H --> I[Depot version finale]
    I --> J[Analyse finale]
    J --> K[Archivage]
```

## Figure 14 - Architecture MVC de Laravel

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Navigateur] --> B[Routes]
    B --> C[Middleware]
    C --> D[Controllers]
    D --> E[Models]
    E --> F[Base de donnees]
    D --> G[Services]
    D --> H[Vues Blade]
    H --> A
```

---

## Note technique — Serveur utilisé

- En développement : `php artisan serve` (serveur PHP intégré de Laravel).
- En production : Apache ou Nginx avec PHP-FPM (préconisé pour les performances et la sécurité).
- Front-end (développement) : `npm run dev` (Vite) pour le rechargement et la compilation des assets.

## Recommendation d'usage

- Utiliser `DIAGRAMMES-PAR-CHAPITRE-NB.md` pour la redaction du memoire.
- Utiliser `DIAGRAMMES-PAR-FIGURE-NB.md` pour la liste des figures et annexes.
