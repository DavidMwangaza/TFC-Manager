# Diagrammes en Noir et Blanc - Version par Chapitre

Ce document regroupe les diagrammes par chapitre du mémoire.
Tous les diagrammes sont en style noir et blanc (fond blanc, traits noirs, texte noir).

## Chapitre II - Section 2 : Diagrammes de cas d'utilisation

### 2.1 Diagramme de cas d'utilisation global

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','secondaryColor':'#ffffff','tertiaryColor':'#ffffff','primaryBorderColor':'#000000','secondaryBorderColor':'#000000','tertiaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000','primaryTextColor':'#000000','secondaryTextColor':'#000000','tertiaryTextColor':'#000000','noteBorderColor':'#000000','noteBkgColor':'#ffffff'}}}%%
graph TB
    Visiteur[Visiteur Public]
    Etudiant[Etudiant]
    ChefDept[Chef de Departement]
    Enseignant[Enseignant]
    Admin[Administrateur]

    UC1[Consulter archives]
    UC2[Rechercher travaux]
    UC3[Telecharger version finale]
    UC4[Soumettre sujet]
    UC5[Deposer fichier TFC]
    UC6[Valider ou rejeter sujet]
    UC7[Assigner encadreur]
    UC8[Autoriser soutenance]
    UC9[Gerer utilisateurs]
    UC10[Gerer facultes et filieres]
    UC11[Gerer annees academiques]

    Visiteur --> UC1
    Visiteur --> UC2
    Visiteur --> UC3

    Etudiant --> UC4
    Etudiant --> UC5

    ChefDept --> UC6
    ChefDept --> UC7

    Enseignant --> UC8

    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
```

### 2.2 Diagramme de cas d'utilisation - Etudiant

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Etudiant]
    U1[S inscrire]
    U2[Se connecter]
    U3[Soumettre sujet en 5 etapes]
    U4[Consulter etat du sujet]
    U5[Deposer version jury]
    U6[Consulter rapport IA]
    U7[Deposer version finale]
    U8[Consulter notifications]

    A --> U1
    A --> U2
    A --> U3
    A --> U4
    A --> U5
    A --> U6
    A --> U7
    A --> U8
```

### 2.3 Diagramme de cas d'utilisation - Chef de Departement

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Chef de Departement]
    U1[Consulter sujets en attente]
    U2[Valider sujet]
    U3[Rejeter sujet avec motif]
    U4[Assigner enseignant encadreur]
    U5[Suivre avancement TFC]
    U6[Consulter rapports IA]
    U7[Exporter liste CSV]

    A --> U1
    A --> U2
    A --> U3
    A --> U4
    A --> U5
    A --> U6
    A --> U7
```

### 2.4 Diagramme de cas d'utilisation - Enseignant

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Enseignant]
    U1[Consulter sujets encadres]
    U2[Telecharger fichiers]
    U3[Consulter rapport IA]
    U4[Demander corrections]
    U5[Autoriser soutenance]

    A --> U1
    A --> U2
    A --> U3
    A --> U4
    A --> U5
```

### 2.5 Diagramme de cas d'utilisation - Administrateur

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Administrateur]
    U1[Creer ou modifier utilisateurs]
    U2[Bloquer ou debloquer utilisateur]
    U3[Reinitialiser mot de passe]
    U4[Gerer facultes]
    U5[Gerer filieres]
    U6[Gerer annees academiques]
    U7[Configurer parametres systeme]
    U8[Consulter journal d activite]

    A --> U1
    A --> U2
    A --> U3
    A --> U4
    A --> U5
    A --> U6
    A --> U7
    A --> U8
```

## Chapitre II - Section 3 : Diagrammes de sequence

### 3.1 Sequence - Soumission de sujet

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000','actorBorder':'#000000','actorBkg':'#ffffff','actorTextColor':'#000000','signalColor':'#000000','signalTextColor':'#000000'}}}%%
sequenceDiagram
    participant E as Etudiant
    participant N as Navigateur
    participant C as SubjectController
    participant DB as Base de donnees
    participant S as Service Notification
    participant CP as Chef de Departement

    E->>N: Remplir formulaire (5 etapes)
    N->>C: POST /subjects
    C->>DB: Verifier absence de sujet en attente ou valide
    DB-->>C: Resultat verification
    C->>DB: Inserer sujet (statut en attente)
    C->>S: Creer notification NewSubjectSubmitted
    S->>CP: Envoyer courriel et notification interne
    C-->>N: Redirection avec message de succes
```

### 3.2 Sequence - Validation ou rejet de sujet

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000','actorBorder':'#000000','actorBkg':'#ffffff','actorTextColor':'#000000','signalColor':'#000000','signalTextColor':'#000000'}}}%%
sequenceDiagram
    participant CP as Chef de Departement
    participant C as SubjectController
    participant DB as Base de donnees
    participant S as Service Notification
    participant E as Etudiant
    participant T as Enseignant

    CP->>C: Valider sujet et choisir enseignant
    C->>DB: Verifier droits et statut en attente
    DB-->>C: Verification OK
    C->>DB: Mettre a jour statut valide et teacher_id
    C->>S: Notifier etudiant
    C->>S: Notifier enseignant
    S->>E: Sujet valide
    S->>T: Assignation comme encadreur

    CP->>C: Rejeter sujet avec motif
    C->>DB: Mettre a jour statut rejete et motif
    C->>S: Notifier etudiant du rejet
```

### 3.3 Sequence - Depot de fichier TFC avec analyse IA

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000','actorBorder':'#000000','actorBkg':'#ffffff','actorTextColor':'#000000','signalColor':'#000000','signalTextColor':'#000000'}}}%%
sequenceDiagram
    participant E as Etudiant
    participant C as ThesisFileController
    participant F as Stockage Fichier
    participant DB as Base de donnees
    participant AI as AiDetectionService
    participant API as API GPTZero
    participant S as Service Notification
    participant T as Enseignant

    E->>C: Deposer PDF (jury ou finale)
    C->>C: Valider format et taille
    C->>F: Stocker fichier
    C->>DB: Inserer thesis_file
    C->>AI: Lancer analyse IA
    AI->>F: Lire PDF
    AI->>API: Envoyer texte extrait
    API-->>AI: Retour des probabilites
    AI->>DB: Inserer ai_report
    C->>S: Notifier enseignant
    S->>T: Fichier depose et rapport disponible
```

### 3.4 Sequence - Autorisation de soutenance

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000','actorBorder':'#000000','actorBkg':'#ffffff','actorTextColor':'#000000','signalColor':'#000000','signalTextColor':'#000000'}}}%%
sequenceDiagram
    participant T as Enseignant
    participant C as SubjectController
    participant DB as Base de donnees
    participant S as Service Notification
    participant E as Etudiant

    T->>C: Autoriser soutenance
    C->>DB: Verifier encadreur et statut valide
    DB-->>C: Verification OK
    C->>DB: defense_validated = true
    C->>S: Creer notification DefenseAuthorized
    S->>E: Autorisation de soutenance accordee
```

## Chapitre II - Section 4 : Diagramme de classes

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
classDiagram
    class Faculty {
        +id
        +name
        +code
    }

    class Department {
        +id
        +faculty_id
        +name
        +code
    }

    class User {
        +id
        +department_id
        +name
        +email
        +is_blocked
    }

    class AcademicYear {
        +id
        +name
        +is_current
        +is_closed
    }

    class Subject {
        +id
        +student_id
        +teacher_id
        +department_id
        +academic_year_id
        +status
        +defense_validated
    }

    class ThesisFile {
        +id
        +subject_id
        +user_id
        +version_type
        +file_path
    }

    class AiReport {
        +id
        +thesis_file_id
        +similarity_score
        +ai_score
    }

    class Notification {
        +id
        +user_id
        +type
        +read_at
    }

    class ActivityLog {
        +id
        +user_id
        +action
        +model_type
        +model_id
    }

    Faculty "1" --> "*" Department
    Department "1" --> "*" User
    Department "1" --> "*" Subject
    AcademicYear "1" --> "*" Subject
    User "1" --> "*" Subject : soumet
    User "1" --> "*" Subject : encadre
    Subject "1" --> "0..2" ThesisFile
    ThesisFile "1" --> "0..1" AiReport
    User "1" --> "*" Notification
    User "1" --> "*" ActivityLog
```

## Chapitre II - Section 5 : Modele relationnel de la base de donnees

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
    USERS ||--o{ NOTIFICATIONS : recoit
    USERS ||--o{ ACTIVITY_LOGS : genere

    FACULTIES {
        int id PK
        string name
        string code
    }
    DEPARTMENTS {
        int id PK
        int faculty_id FK
        string name
        string code
    }
    USERS {
        int id PK
        int department_id FK
        string name
        string email
    }
    SUBJECTS {
        int id PK
        int student_id FK
        int teacher_id FK
        int department_id FK
        int academic_year_id FK
        string status
    }
    THESIS_FILES {
        int id PK
        int subject_id FK
        int user_id FK
        string version_type
    }
    AI_REPORTS {
        int id PK
        int thesis_file_id FK
        float ai_score
    }
```

## Chapitre II - Section 6 : Diagramme d'activites (processus complet)

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph TD
    A[Debut] --> B[Soumission du sujet]
    B --> C{Sujet valide par le Chef?}
    C -->|Non| D[Rejet avec motif]
    D --> B
    C -->|Oui| E[Assignation de l enseignant]
    E --> F[Depot version jury]
    F --> G[Analyse IA automatique]
    G --> H{Corrections demandees?}
    H -->|Oui| I[Corrections et redepot]
    I --> F
    H -->|Non| J[Autorisation de soutenance]
    J --> K[Depot version finale]
    K --> L[Analyse IA finale]
    L --> M[Memoire pret pour soutenance]
    M --> N[Archivage]
    N --> O[Fin]
```

## Chapitre III - Section 2 : Architecture MVC de Laravel

```mermaid
%%{init: {'theme':'base','themeVariables': {'background':'#ffffff','primaryColor':'#ffffff','primaryBorderColor':'#000000','lineColor':'#000000','textColor':'#000000'}}}%%
graph LR
    A[Navigateur] --> B[Routes web.php]
    B --> C[Middleware]
    C --> D[Controller]
    D --> E[Models Eloquent]
    E --> F[Base de donnees]
    D --> G[Services metier]
    G --> H[API externe GPTZero]
    D --> I[Vues Blade]
    I --> A
    D --> J[Notifications]
    J --> A
```

---

## Notes d'impression

- Theme monochrome applique a chaque diagramme.
- Impression recommandee en niveaux de gris.
- Compatible avec export PDF depuis VS Code ou GitHub.
