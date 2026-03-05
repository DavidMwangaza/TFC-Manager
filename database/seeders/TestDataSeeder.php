<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\AiReport;
use App\Models\Subject;
use App\Models\SystemSetting;
use App\Models\ThesisFile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestDataSeeder extends Seeder
{
    /**
     * Données de test complètes pour l'année académique 2025-2026.
     */
    public function run(): void
    {
        // =====================================================
        // 1. ANNÉE ACADÉMIQUE 2025-2026
        // =====================================================
        $annee = AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-10-01',
            'end_date' => '2026-09-30',
            'is_current' => true,
            'is_closed' => false,
        ]);

        // =====================================================
        // 2. PARAMÈTRES SYSTÈME (dates limites)
        // =====================================================
        SystemSetting::set('subject_deadline', '2026-03-31');
        SystemSetting::set('thesis_deadline', '2026-06-30');
        SystemSetting::set('final_deposit_deadline', '2026-08-15');

        // =====================================================
        // 3. RÉCUPÉRER LES UTILISATEURS EXISTANTS
        // =====================================================
        $admin = User::where('email', 'admin@udbl-tfc.cd')->first();
        $cpGL = User::where('email', 'cp.gl@udbl-tfc.cd')->first();
        $cpRAS = User::where('email', 'cp.ras@udbl-tfc.cd')->first();
        $cpECOPO = User::where('email', 'cp.ecopo@udbl-tfc.cd')->first();
        $prof1 = User::where('email', 'prof1@udbl-tfc.cd')->first();
        $prof2 = User::where('email', 'prof2@udbl-tfc.cd')->first();
        $prof3 = User::where('email', 'prof3@udbl-tfc.cd')->first();
        $etudiant1 = User::where('email', 'etudiant1@udbl-tfc.cd')->first();
        $etudiant2 = User::where('email', 'etudiant2@udbl-tfc.cd')->first();
        $etudiant3 = User::where('email', 'etudiant3@udbl-tfc.cd')->first();
        $etudiant4 = User::where('email', 'etudiant4@udbl-tfc.cd')->first();

        // =====================================================
        // 4. SUJETS DE TFC — DIFFÉRENTS STATUTS
        // =====================================================

        // --- Sujet 1 : VALIDÉ + enseignant assigné + fichier TFC + rapport IA (Génie Logiciel) ---
        $sujet1 = Subject::create([
            'title' => 'Conception et développement d\'une plateforme web de gestion des TFC à l\'UDBL',
            'subject_type' => 'tfc',
            'description' => 'Ce travail porte sur la conception et le développement d\'une application web permettant la gestion complète du processus de soumission, validation et suivi des travaux de fin de cycle au sein de l\'Université Don Bosco de Lubumbashi.',
            'context_relevance' => 'L\'UDBL gère chaque année un nombre croissant de TFC. Le processus actuel, basé sur des échanges papier et des tableaux Excel, engendre des retards, des pertes de documents et un manque de traçabilité. Une plateforme numérique dédiée permettrait d\'optimiser ce processus.',
            'challenges' => 'Absence de système centralisé, difficulté de suivi en temps réel, risque de plagiat non détecté, communication fragmentée entre étudiants et encadreurs.',
            'research_question' => 'Comment concevoir une plateforme web capable d\'automatiser et de sécuriser le processus de gestion des TFC à l\'UDBL ?',
            'hypothesis' => 'L\'implémentation d\'une plateforme web intégrant la validation par workflow et la détection IA de plagiat améliorerait significativement l\'efficacité et la transparence de la gestion des TFC.',
            'general_objective' => 'Développer une application web complète pour la gestion des TFC à l\'UDBL.',
            'specific_objectives' => [
                'Analyser le processus actuel de gestion des TFC',
                'Concevoir l\'architecture de la plateforme',
                'Implémenter les modules de soumission, validation et suivi',
                'Intégrer un système de détection de plagiat par IA',
                'Tester et déployer la solution',
            ],
            'state_of_art' => [
                ['author' => 'Kasongo M.', 'year' => 2023, 'title' => 'Digitalisation des processus académiques en RDC', 'contribution' => 'Étude des défis numériques dans les universités congolaises'],
                ['author' => 'Ndala P.', 'year' => 2022, 'title' => 'Systèmes de gestion documentaire pour institutions', 'contribution' => 'Architecture de référence pour le stockage et la traçabilité des documents'],
            ],
            'demarcations' => 'Ce travail se limite à la gestion des TFC (pas les mémoires de Master) et se concentre sur l\'UDBL. L\'IA utilisée est un service tiers d\'analyse textuelle, pas un modèle entraîné sur place.',
            'methodologies' => 'Méthode agile SCRUM, UML pour la modélisation, Laravel + Tailwind CSS pour le développement, tests unitaires et d\'intégration.',
            'status' => 'validated',
            'rejection_reason' => null,
            'defense_validated' => true,
            'student_id' => $etudiant1->id,
            'teacher_id' => $prof1->id,
            'department_id' => $etudiant1->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-01-10 09:00:00',
            'updated_at' => '2026-01-25 14:30:00',
        ]);

        // Créer les fichiers PDF fictifs sur le disque
        Storage::disk('public')->makeDirectory('thesis_files');
        $dummyPdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n0\n%%EOF";
        foreach (['thesis_files/tfc_mulongo_david_v_jury.pdf', 'thesis_files/tfc_mulongo_david_v_final.pdf', 'thesis_files/tfc_kapend_josue_v_jury.pdf'] as $fakePath) {
            Storage::disk('public')->put($fakePath, $dummyPdf);
        }

        // Fichier TFC version jury pour sujet 1
        $thesisFile1 = ThesisFile::create([
            'subject_id' => $sujet1->id,
            'file_path' => 'thesis_files/tfc_mulongo_david_v_jury.pdf',
            'original_name' => 'TFC_Mulongo_David_Gestion_TFC.pdf',
            'version_type' => 'jury',
            'created_at' => '2026-02-01 10:00:00',
        ]);

        // Rapport IA — Score bas (bon)
        AiReport::create([
            'thesis_file_id' => $thesisFile1->id,
            'similarity_score' => 12,
            'ai_score' => 8,
            'details' => [
                'analysis_date' => '2026-02-01',
                'total_pages' => 85,
                'flagged_sections' => [
                    ['page' => 15, 'type' => 'citation_correcte', 'severity' => 'info', 'text' => 'Citation conforme aux normes APA'],
                ],
                'summary' => 'Le document présente un faible taux de similarité et un score IA minimal. Le travail semble original et bien rédigé.',
            ],
        ]);

        // Fichier TFC version finale pour sujet 1
        $thesisFile1Final = ThesisFile::create([
            'subject_id' => $sujet1->id,
            'file_path' => 'thesis_files/tfc_mulongo_david_v_final.pdf',
            'original_name' => 'TFC_Mulongo_David_FINAL.pdf',
            'version_type' => 'final',
            'created_at' => '2026-02-10 08:00:00',
        ]);

        AiReport::create([
            'thesis_file_id' => $thesisFile1Final->id,
            'similarity_score' => 10,
            'ai_score' => 6,
            'details' => [
                'analysis_date' => '2026-02-10',
                'total_pages' => 92,
                'flagged_sections' => [],
                'summary' => 'Version finale validée. Aucun problème détecté.',
            ],
        ]);

        // --- Sujet 2 : VALIDÉ + enseignant assigné, en attente de dépôt (Génie Logiciel) ---
        $sujet2 = Subject::create([
            'title' => 'Mise en place d\'un système de gestion des présences par reconnaissance faciale',
            'subject_type' => 'tfc',
            'description' => 'Développement d\'un système automatisé de suivi des présences utilisant la reconnaissance faciale pour les cours magistraux à l\'UDBL.',
            'context_relevance' => 'La gestion manuelle des présences est chronophage et peu fiable. Un système biométrique permettrait une traçabilité exacte.',
            'challenges' => 'Conditions d\'éclairage variables, protection des données biométriques, acceptabilité par les étudiants.',
            'research_question' => 'Comment implémenter un système de reconnaissance faciale fiable pour la gestion des présences dans un contexte universitaire congolais ?',
            'hypothesis' => 'Un système basé sur les réseaux de neurones convolutifs peut atteindre un taux de reconnaissance supérieur à 95% dans les conditions de l\'UDBL.',
            'general_objective' => 'Concevoir et déployer un système de gestion des présences par reconnaissance faciale.',
            'specific_objectives' => [
                'Étudier les algorithmes de reconnaissance faciale existants',
                'Collecter un dataset de visages d\'étudiants',
                'Développer et entraîner le modèle',
                'Intégrer le système dans l\'environnement existant',
            ],
            'state_of_art' => [
                ['author' => 'Turk M. & Pentland A.', 'year' => 2021, 'title' => 'Face recognition using eigenfaces', 'contribution' => 'Base fondamentale de la reconnaissance faciale'],
            ],
            'demarcations' => 'Limité à un département pilote, sans extension au contrôle d\'accès physique.',
            'methodologies' => 'Méthode expérimentale, Python + OpenCV + TensorFlow, tests de performance.',
            'status' => 'validated',
            'rejection_reason' => null,
            'defense_validated' => false,
            'student_id' => $etudiant2->id,
            'teacher_id' => $prof2->id,
            'department_id' => $etudiant2->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-01-15 11:00:00',
            'updated_at' => '2026-01-28 16:00:00',
        ]);

        // --- Sujet 3 : EN ATTENTE de validation (Réseaux) ---
        $sujet3 = Subject::create([
            'title' => 'Déploiement d\'une infrastructure réseau sécurisée pour un campus universitaire',
            'subject_type' => 'tfc',
            'description' => 'Étude et mise en place d\'une architecture réseau sécurisée intégrant segmentation VLAN, pare-feu et VPN pour le campus de l\'UDBL.',
            'context_relevance' => 'Le réseau actuel du campus est non segmenté, vulnérable aux attaques et au trafic non autorisé. Une refonte s\'impose.',
            'challenges' => 'Budget limité, manque d\'expertise locale, nécessité de compatibilité avec l\'existant.',
            'research_question' => 'Comment concevoir une infrastructure réseau à la fois sécurisée et adaptée aux contraintes budgétaires d\'une université congolaise ?',
            'hypothesis' => 'L\'utilisation d\'outils open source (pfSense, OpenVPN) combinée à une architecture VLAN bien pensée peut offrir un niveau de sécurité satisfaisant à moindre coût.',
            'general_objective' => 'Proposer et implémenter une architecture réseau sécurisée pour le campus UDBL.',
            'specific_objectives' => [
                'Auditer l\'infrastructure réseau existante',
                'Concevoir l\'architecture cible avec segmentation',
                'Déployer les solutions de sécurité (firewall, VPN)',
                'Tester les performances et la résilience',
            ],
            'state_of_art' => [],
            'demarcations' => 'Limité au bâtiment principal du campus, hors connexions inter-sites.',
            'methodologies' => 'Méthode Top-Down Network Design, simulation Packet Tracer, déploiement pilote.',
            'status' => 'pending',
            'rejection_reason' => null,
            'defense_validated' => false,
            'student_id' => $etudiant3->id,
            'teacher_id' => null,
            'department_id' => $etudiant3->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-02-05 08:30:00',
            'updated_at' => '2026-02-05 08:30:00',
        ]);

        // --- Sujet 4 : REJETÉ (ECOPO) ---
        $sujet4 = Subject::create([
            'title' => 'Analyse de l\'impact du commerce électronique sur les PME de Lubumbashi',
            'subject_type' => 'tfc',
            'description' => 'Ce travail étudie l\'impact de l\'adoption du commerce électronique sur la performance financière et opérationnelle des petites et moyennes entreprises de Lubumbashi.',
            'context_relevance' => 'Le commerce électronique se développe rapidement en RDC, mais son impact réel sur les PME locales reste peu documenté.',
            'challenges' => 'Manque de données fiables, résistance au changement des PME, infrastructure numérique limitée.',
            'research_question' => 'Quel est l\'impact réel du commerce électronique sur la croissance des PME à Lubumbashi ?',
            'hypothesis' => 'Les PME ayant adopté le e-commerce enregistrent une croissance de leur chiffre d\'affaires significativement supérieure à celles qui ne l\'ont pas fait.',
            'general_objective' => 'Évaluer l\'impact du commerce électronique sur les PME de Lubumbashi.',
            'specific_objectives' => [
                'Identifier les PME utilisant le e-commerce à Lubumbashi',
                'Mesurer les indicateurs de performance',
                'Comparer les performances avec les PME traditionnelles',
            ],
            'state_of_art' => [],
            'demarcations' => 'Limité aux PME enregistrées de Lubumbashi, secteur de détail uniquement.',
            'methodologies' => 'Enquête quantitative, échantillonnage aléatoire, analyse SPSS.',
            'status' => 'rejected',
            'rejection_reason' => 'Le sujet est trop vague et ne se démarque pas suffisamment des études existantes. Veuillez préciser le secteur d\'activité étudié et intégrer une dimension numérique/technologique plus forte. Pensez à comparer avec des études similaires dans d\'autres villes de la RDC.',
            'defense_validated' => false,
            'student_id' => $etudiant4->id,
            'teacher_id' => null,
            'department_id' => $etudiant4->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-01-20 14:00:00',
            'updated_at' => '2026-02-01 09:00:00',
        ]);

        // --- Sujet 5 : VALIDÉ + fichier avec score IA élevé (alerte plagiat) (Génie Logiciel) ---
        // Ajoutons un 5ème étudiant pour ce cas
        $etudiant5 = User::create([
            'name' => 'Josué Kapend',
            'email' => 'etudiant5@udbl-tfc.cd',
            'password' => bcrypt('password'),
            'matricule' => 'ETU-005',
            'department_id' => $etudiant1->department_id,
            'email_verified_at' => now(),
        ]);
        $etudiant5->assignRole('Etudiant');

        $sujet5 = Subject::create([
            'title' => 'Développement d\'une application mobile de gestion des notes académiques',
            'subject_type' => 'tfc',
            'description' => 'Conception d\'une application mobile Android permettant aux étudiants de consulter leurs notes et aux enseignants de les saisir en temps réel.',
            'context_relevance' => 'Les étudiants doivent se déplacer physiquement pour consulter leurs résultats. Une application mobile simplifierait l\'accès à l\'information.',
            'challenges' => 'Connexion internet instable, diversité des appareils Android, sécurité des données académiques.',
            'research_question' => 'Comment développer une application mobile de consultation des notes qui fonctionne de manière fiable même en contexte de connectivité limitée ?',
            'hypothesis' => 'L\'utilisation du mode offline-first couplé à une synchronisation asynchrone permettrait un accès fiable aux notes même en zone à faible connectivité.',
            'general_objective' => 'Développer une application mobile de gestion des notes pour l\'UDBL.',
            'specific_objectives' => [
                'Analyser les besoins des utilisateurs',
                'Concevoir l\'architecture mobile avec mode offline',
                'Développer l\'application avec Flutter',
                'Tester sur différents appareils',
            ],
            'state_of_art' => [
                ['author' => 'Mutombo J.', 'year' => 2024, 'title' => 'Applications mobiles éducatives en Afrique', 'contribution' => 'Revue des solutions existantes'],
            ],
            'demarcations' => 'Application limitée à Android, une seule faculté pilote.',
            'methodologies' => 'Méthode RAD, Flutter + Firebase, tests utilisateurs.',
            'status' => 'validated',
            'rejection_reason' => null,
            'defense_validated' => false,
            'student_id' => $etudiant5->id,
            'teacher_id' => $prof1->id,
            'department_id' => $etudiant5->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-01-12 10:00:00',
            'updated_at' => '2026-01-30 11:00:00',
        ]);

        // Fichier TFC avec score IA élevé (suspicion de plagiat)
        $thesisFile5 = ThesisFile::create([
            'subject_id' => $sujet5->id,
            'file_path' => 'thesis_files/tfc_kapend_josue_v_jury.pdf',
            'original_name' => 'TFC_Kapend_Josue_App_Mobile.pdf',
            'version_type' => 'jury',
            'created_at' => '2026-02-08 14:00:00',
        ]);

        AiReport::create([
            'thesis_file_id' => $thesisFile5->id,
            'similarity_score' => 62,
            'ai_score' => 71,
            'details' => [
                'analysis_date' => '2026-02-08',
                'total_pages' => 68,
                'flagged_sections' => [
                    ['page' => 8, 'type' => 'similarite_elevee', 'severity' => 'high', 'text' => 'Section « État de l\'art » présentant une forte similarité avec un mémoire publié en 2024.'],
                    ['page' => 22, 'type' => 'contenu_ia', 'severity' => 'high', 'text' => 'Le chapitre méthodologique semble généré par un outil IA (cohérence stylistique atypique).'],
                    ['page' => 35, 'type' => 'similarite_moderee', 'severity' => 'medium', 'text' => 'Passages similaires à un article en ligne sans citation.'],
                ],
                'summary' => 'ALERTE : Le document présente un taux de similarité élevé (62%) et un score IA important (71%). Plusieurs sections semblent copiées ou générées par IA. Une vérification approfondie est recommandée.',
            ],
        ]);

        // --- Sujet 6 : EN ATTENTE — vient d'être soumis (Réseaux) ---
        $etudiant6 = User::create([
            'name' => 'Alice Mwamba',
            'email' => 'etudiant6@udbl-tfc.cd',
            'password' => bcrypt('password'),
            'matricule' => 'ETU-006',
            'department_id' => $etudiant3->department_id,
            'email_verified_at' => now(),
        ]);
        $etudiant6->assignRole('Etudiant');

        $sujet6 = Subject::create([
            'title' => 'Implémentation d\'un système de monitoring réseau basé sur Zabbix',
            'subject_type' => 'tfc',
            'description' => 'Mise en place d\'une solution de surveillance réseau centralisée utilisant Zabbix pour le suivi en temps réel des équipements du campus.',
            'context_relevance' => 'L\'absence de monitoring centralisé rend la détection des pannes lente et la maintenance réactive.',
            'challenges' => 'Volume de données à traiter, configuration SNMP des équipements hétérogènes, alertes pertinentes.',
            'research_question' => 'Comment déployer un système de monitoring efficace dans un environnement réseau hétérogène à ressources limitées ?',
            'hypothesis' => 'Zabbix, grâce à sa flexibilité et son coût zéro, constitue une solution viable pour le monitoring réseau universitaire en RDC.',
            'general_objective' => 'Déployer et configurer un système Zabbix pour le monitoring du réseau UDBL.',
            'specific_objectives' => [
                'Inventorier les équipements réseau du campus',
                'Installer et configurer Zabbix Server',
                'Définir les métriques et seuils d\'alerte',
                'Tester et valider en environnement de production',
            ],
            'state_of_art' => [],
            'demarcations' => 'Monitoring réseau uniquement, pas de monitoring applicatif.',
            'methodologies' => 'Approche expérimentale, déploiement Zabbix sur VM Linux, tests de charge.',
            'status' => 'pending',
            'rejection_reason' => null,
            'defense_validated' => false,
            'student_id' => $etudiant6->id,
            'teacher_id' => null,
            'department_id' => $etudiant6->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-02-12 16:00:00',
            'updated_at' => '2026-02-12 16:00:00',
        ]);

        // =====================================================
        // 5. NOTIFICATIONS DE TEST
        // =====================================================

        // --- Sujet 7 : EN ATTENTE (Génie Logiciel) — pour tester validation par CP GL ---
        $etudiant7 = User::create([
            'name' => 'Merveille Kashala',
            'email' => 'etudiant7@udbl-tfc.cd',
            'password' => bcrypt('password'),
            'matricule' => 'ETU-007',
            'department_id' => $etudiant1->department_id,
            'email_verified_at' => now(),
        ]);
        $etudiant7->assignRole('Etudiant');

        $sujet7 = Subject::create([
            'title' => 'Conception d\'un chatbot intelligent pour l\'assistance académique des étudiants',
            'subject_type' => 'tfc',
            'description' => 'Développement d\'un chatbot alimenté par l\'IA pour répondre aux questions fréquentes des étudiants concernant les inscriptions, les horaires et les procédures académiques.',
            'context_relevance' => 'Les étudiants de l\'UDBL perdent beaucoup de temps à chercher des informations administratives. Un chatbot disponible 24h/24 réduirait la charge du secrétariat.',
            'challenges' => 'Qualité des réponses en français congolais, disponibilité du serveur, maintenance de la base de connaissances.',
            'research_question' => 'Comment concevoir un chatbot capable de fournir des réponses fiables aux questions académiques dans le contexte de l\'UDBL ?',
            'hypothesis' => 'Un chatbot basé sur le NLP et une base de connaissances structurée peut répondre correctement à plus de 80% des questions fréquentes des étudiants.',
            'general_objective' => 'Développer un assistant virtuel intelligent pour l\'accompagnement académique à l\'UDBL.',
            'specific_objectives' => [
                'Recenser les questions fréquemment posées par les étudiants',
                'Concevoir l\'architecture du chatbot avec NLP',
                'Développer le chatbot avec Python et Rasa',
                'Tester et évaluer la satisfaction des utilisateurs',
            ],
            'state_of_art' => [
                ['author' => 'Kalala R.', 'year' => 2024, 'title' => 'Chatbots dans l\'enseignement supérieur en Afrique', 'contribution' => 'Analyse des solutions chatbot déployées dans les universités africaines'],
            ],
            'demarcations' => 'Limité aux questions académiques, pas de support technique ou financier.',
            'methodologies' => 'Méthode itérative, Python + Rasa NLU, collecte de données par questionnaire.',
            'status' => 'pending',
            'rejection_reason' => null,
            'defense_validated' => false,
            'student_id' => $etudiant7->id,
            'teacher_id' => null,
            'department_id' => $etudiant7->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-02-11 10:00:00',
            'updated_at' => '2026-02-11 10:00:00',
        ]);

        // --- Sujet 8 : EN ATTENTE (Génie Logiciel) — pour tester rejet par CP GL ---
        $etudiant8 = User::create([
            'name' => 'Béni Tshilombo',
            'email' => 'etudiant8@udbl-tfc.cd',
            'password' => bcrypt('password'),
            'matricule' => 'ETU-008',
            'department_id' => $etudiant1->department_id,
            'email_verified_at' => now(),
        ]);
        $etudiant8->assignRole('Etudiant');

        $sujet8 = Subject::create([
            'title' => 'Création d\'un site web pour une entreprise',
            'subject_type' => 'tfc',
            'description' => 'Ce travail consiste à créer un site web vitrine pour une entreprise locale de Lubumbashi.',
            'context_relevance' => 'Les entreprises locales ont besoin de visibilité en ligne.',
            'challenges' => 'Manque de contenu fourni par l\'entreprise.',
            'research_question' => 'Comment créer un site web pour une entreprise ?',
            'hypothesis' => 'Un site web améliorera la visibilité de l\'entreprise.',
            'general_objective' => 'Créer un site web vitrine.',
            'specific_objectives' => [
                'Définir les besoins du client',
                'Développer le site web',
                'Mettre en ligne',
            ],
            'state_of_art' => [],
            'demarcations' => 'Limité à un site vitrine statique.',
            'methodologies' => 'HTML, CSS, hébergement gratuit.',
            'status' => 'pending',
            'rejection_reason' => null,
            'defense_validated' => false,
            'student_id' => $etudiant8->id,
            'teacher_id' => null,
            'department_id' => $etudiant8->department_id,
            'academic_year_id' => $annee->id,
            'created_at' => '2026-02-13 08:00:00',
            'updated_at' => '2026-02-13 08:00:00',
        ]);

        // Notification pour le chef de département GL : nouveaux sujets soumis
        $cpGL->notify(new \App\Notifications\NewSubjectSubmitted($sujet7));
        $cpGL->notify(new \App\Notifications\NewSubjectSubmitted($sujet8));

        // Notification pour le chef de département GL : ancien sujet réseau (erreur volontaire - corrigé)
        $cpRAS->notify(new \App\Notifications\NewSubjectSubmitted($sujet3));

        // Notification pour l'étudiant 1 : sujet validé
        $etudiant1->notify(new \App\Notifications\SubjectValidated($sujet1));

        // Notification pour l'étudiant 4 : sujet rejeté
        $etudiant4->notify(new \App\Notifications\SubjectRejected($sujet4));

        // Notification pour l'étudiant 2 : enseignant assigné
        $etudiant2->notify(new \App\Notifications\TeacherAssigned($sujet2));

        // Notification pour le prof1 : fichier TFC déposé
        $prof1->notify(new \App\Notifications\ThesisFileUploaded($thesisFile1));

        // Notification pour l'étudiant 1 : défense autorisée
        $etudiant1->notify(new \App\Notifications\DefenseAuthorized($sujet1));

        // =====================================================
        // 6. JOURNAL D'ACTIVITÉ
        // =====================================================
        $logs = [
            [
                'user_id' => $admin->id,
                'action' => 'create',
                'model_type' => 'App\\Models\\AcademicYear',
                'model_id' => $annee->id,
                'description' => 'Création de l\'année académique 2025-2026',
                'old_values' => null,
                'new_values' => ['name' => '2025-2026'],
                'ip_address' => '192.168.1.10',
                'created_at' => '2025-10-01 08:00:00',
            ],
            [
                'user_id' => $etudiant1->id,
                'action' => 'create',
                'model_type' => 'App\\Models\\Subject',
                'model_id' => $sujet1->id,
                'description' => 'Soumission du sujet "Conception et développement d\'une plateforme web de gestion des TFC"',
                'old_values' => null,
                'new_values' => ['title' => $sujet1->title, 'status' => 'pending'],
                'ip_address' => '192.168.1.50',
                'created_at' => '2026-01-10 09:00:00',
            ],
            [
                'user_id' => $cpGL->id,
                'action' => 'update',
                'model_type' => 'App\\Models\\Subject',
                'model_id' => $sujet1->id,
                'description' => 'Validation du sujet de David Mulongo',
                'old_values' => ['status' => 'pending'],
                'new_values' => ['status' => 'validated', 'teacher_id' => $prof1->id],
                'ip_address' => '192.168.1.20',
                'created_at' => '2026-01-25 14:30:00',
            ],
            [
                'user_id' => $etudiant1->id,
                'action' => 'create',
                'model_type' => 'App\\Models\\ThesisFile',
                'model_id' => $thesisFile1->id,
                'description' => 'Dépôt du fichier TFC (version jury)',
                'old_values' => null,
                'new_values' => ['original_name' => 'TFC_Mulongo_David_Gestion_TFC.pdf', 'version_type' => 'jury'],
                'ip_address' => '192.168.1.50',
                'created_at' => '2026-02-01 10:00:00',
            ],
            [
                'user_id' => $prof1->id,
                'action' => 'update',
                'model_type' => 'App\\Models\\Subject',
                'model_id' => $sujet1->id,
                'description' => 'Autorisation de soutenance pour David Mulongo',
                'old_values' => ['defense_validated' => false],
                'new_values' => ['defense_validated' => true],
                'ip_address' => '192.168.1.30',
                'created_at' => '2026-02-05 11:00:00',
            ],
            [
                'user_id' => $cpECOPO->id,
                'action' => 'update',
                'model_type' => 'App\\Models\\Subject',
                'model_id' => $sujet4->id,
                'description' => 'Rejet du sujet d\'Esther Ilunga : sujet trop vague',
                'old_values' => ['status' => 'pending'],
                'new_values' => ['status' => 'rejected'],
                'ip_address' => '192.168.1.22',
                'created_at' => '2026-02-01 09:00:00',
            ],
            [
                'user_id' => $admin->id,
                'action' => 'create',
                'model_type' => 'App\\Models\\User',
                'model_id' => $etudiant5->id,
                'description' => 'Création du compte étudiant Josué Kapend',
                'old_values' => null,
                'new_values' => ['name' => 'Josué Kapend', 'role' => 'Etudiant'],
                'ip_address' => '192.168.1.10',
                'created_at' => '2026-01-08 10:00:00',
            ],
            [
                'user_id' => $admin->id,
                'action' => 'update',
                'model_type' => 'App\\Models\\SystemSetting',
                'model_id' => null,
                'description' => 'Mise à jour des dates limites pour l\'année 2025-2026',
                'old_values' => ['subject_deadline' => null],
                'new_values' => ['subject_deadline' => '2026-03-31'],
                'ip_address' => '192.168.1.10',
                'created_at' => '2025-10-15 09:00:00',
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }

        $this->command->info('');
        $this->command->info('✅ Données de test créées avec succès !');
        $this->command->info('');
        $this->command->info('📋 COMPTES DE CONNEXION (mot de passe : password)');
        $this->command->info('─────────────────────────────────────────────────');
        $this->command->info('👤 Admin           : admin@udbl-tfc.cd');
        $this->command->info('👨‍🏫 Chef Dept GL    : cp.gl@udbl-tfc.cd');
        $this->command->info('👨‍🏫 Chef Dept RAS   : cp.ras@udbl-tfc.cd');
        $this->command->info('👨‍🏫 Chef Dept ECOPO : cp.ecopo@udbl-tfc.cd');
        $this->command->info('📚 Enseignant 1    : prof1@udbl-tfc.cd');
        $this->command->info('📚 Enseignant 2    : prof2@udbl-tfc.cd');
        $this->command->info('📚 Enseignant 3    : prof3@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 1      : etudiant1@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 2      : etudiant2@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 3      : etudiant3@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 4      : etudiant4@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 5      : etudiant5@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 6      : etudiant6@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 7      : etudiant7@udbl-tfc.cd');
        $this->command->info('🎓 Étudiant 8      : etudiant8@udbl-tfc.cd');
        $this->command->info('─────────────────────────────────────────────────');
        $this->command->info('');
        $this->command->info('📊 Données créées :');
        $this->command->info('   • 1 année académique (2025-2026)');
        $this->command->info('   • 8 sujets (2 validés+encadrés, 4 en attente, 1 rejeté, 1 avec alerte IA)');
        $this->command->info('   • 3 fichiers TFC (avec rapports IA)');
        $this->command->info('   • Notifications + journal d\'activité');
    }
}
