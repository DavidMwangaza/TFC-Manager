<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =====================================================
        // FACULTÉS
        // =====================================================
        $esis = Faculty::create([
            'name' => 'ESIS',
            'code' => 'FSI',
            'description' => 'Faculté des Sciences Informatiques',
        ]);

        $ecopo = Faculty::create([
            'name' => 'ECOPO',
            'code' => 'ECOPO',
            'description' => 'Faculté de Gestion et Ingénierie Financière',
        ]);

        $kansebula = Faculty::create([
            'name' => 'KANSEBULA',
            'code' => 'KANSEBULA',
            'description' => 'Faculté des Sciences de l\'Homme et de la Société',
        ]);

        $theologicum = Faculty::create([
            'name' => 'THEOLOGICUM',
            'code' => 'THEOLOGICUM',
            'description' => 'Faculté de Théologie',
        ]);

        // =====================================================
        // FACULTÉ DES SCIENCES INFORMATIQUES (ESIS)
        // =====================================================
        $genieLogiciel = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'Génie Logiciel',
            'code' => 'GL',
            'description' => 'Systèmes informatiques et Gestion informatique',
        ]);

        $reseaux = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'Réseaux et Administration Système',
            'code' => 'AS',
            'description' => 'Réseaux, Administration système et Télécommunications',
        ]);

        $design = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'Design et Multimédia',
            'code' => 'DSN',
            'description' => 'Design graphique et Multimédia',
        ]);

        $msi = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'Management des Systèmes d\'Information',
            'code' => 'MSI',
            'description' => 'Management des Systèmes d\'Information',
        ]);

        $dataScience = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'Data Science',
            'code' => 'DS',
            'description' => 'Master spécialisé en Data Science',
        ]);

        $devops = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'DevOps et Sécurité',
            'code' => 'DEVOPS',
            'description' => 'Master spécialisé en DevOps et Sécurité informatique',
        ]);

        $comNum = Department::create([
            'faculty_id' => $esis->id,
            'name' => 'Communication Numérique',
            'code' => 'CN',
            'description' => 'Master spécialisé en Communication numérique',
        ]);

        // =====================================================
        // FACULTÉ DE GESTION ET INGÉNIERIE FINANCIÈRE (ECOPO)
        // =====================================================
        $gestionEntreprise = Department::create([
            'faculty_id' => $ecopo->id,
            'name' => 'Gestion des Entreprises et Ingénierie Financière',
            'code' => 'GEIF',
            'description' => 'Gestion des entreprises et ingénierie financière',
        ]);

        $marketing = Department::create([
            'faculty_id' => $ecopo->id,
            'name' => 'Management Commercial et Marketing',
            'code' => 'MCM',
            'description' => 'Management commercial et marketing',
        ]);

        $affairesPubliques = Department::create([
            'faculty_id' => $ecopo->id,
            'name' => 'Gestion des Affaires Publiques',
            'code' => 'GAP',
            'description' => 'Gestion des affaires publiques',
        ]);

        $agroAlimentaire = Department::create([
            'faculty_id' => $ecopo->id,
            'name' => 'Diversification et Développement Agro-Alimentaire',
            'code' => 'DDAI',
            'description' => 'Diversification et développement agro-alimentaire et industrie',
        ]);

        // =====================================================
        // FACULTÉ DES SCIENCES DE L'HOMME ET DE LA SOCIÉTÉ (KANSEBULA)
        // =====================================================
        $sciencesHumaines = Department::create([
            'faculty_id' => $kansebula->id,
            'name' => 'Sciences de l\'Homme et de la Société',
            'code' => 'SHS',
            'description' => 'Disciplines centrées sur l\'humain et le développement social',
        ]);

        // =====================================================
        // FACULTÉ DE THÉOLOGIE (THEOLOGICUM)
        // =====================================================
        $theologie = Department::create([
            'faculty_id' => $theologicum->id,
            'name' => 'Théologie',
            'code' => 'THEO',
            'description' => 'Sciences religieuses et théologiques',
        ]);

        // =====================================================
        // UTILISATEURS
        // =====================================================

        // Admin
        $admin = User::create([
            'name' => 'Administrateur Système',
            'email' => 'admin@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ADM-001',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Chef de Département — Génie Logiciel (ESIS)
        $cpGL = User::create([
            'name' => 'Prof. Jean Kabongo',
            'email' => 'cp.gl@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'CP-001',
            'department_id' => $genieLogiciel->id,
            'email_verified_at' => now(),
        ]);
        $cpGL->assignRole('Chef Departement');

        // Chef de Département — Réseaux (ESIS)
        $cpRAS = User::create([
            'name' => 'Prof. Sylvie Kyungu',
            'email' => 'cp.ras@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'CP-002',
            'department_id' => $reseaux->id,
            'email_verified_at' => now(),
        ]);
        $cpRAS->assignRole('Chef Departement');

        // Chef de Département — ECOPO
        $cpECOPO = User::create([
            'name' => 'Prof. Joseph Kalala',
            'email' => 'cp.ecopo@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'CP-003',
            'department_id' => $gestionEntreprise->id,
            'email_verified_at' => now(),
        ]);
        $cpECOPO->assignRole('Chef Departement');

        // Enseignants — Génie Logiciel
        $prof1 = User::create([
            'name' => 'Prof. Marie Lukusa',
            'email' => 'prof1@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ENS-001',
            'department_id' => $genieLogiciel->id,
            'email_verified_at' => now(),
        ]);
        $prof1->assignRole('Enseignant');

        $prof2 = User::create([
            'name' => 'Prof. Patrick Mbuyi',
            'email' => 'prof2@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ENS-002',
            'department_id' => $genieLogiciel->id,
            'email_verified_at' => now(),
        ]);
        $prof2->assignRole('Enseignant');

        // Enseignant — Réseaux
        $prof3 = User::create([
            'name' => 'Prof. Claude Ngoy',
            'email' => 'prof3@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ENS-003',
            'department_id' => $reseaux->id,
            'email_verified_at' => now(),
        ]);
        $prof3->assignRole('Enseignant');

        // Étudiants — Génie Logiciel
        $etudiant1 = User::create([
            'name' => 'David Mulongo',
            'email' => 'etudiant1@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ETU-001',
            'department_id' => $genieLogiciel->id,
            'email_verified_at' => now(),
        ]);
        $etudiant1->assignRole('Etudiant');

        $etudiant2 = User::create([
            'name' => 'Grace Katumba',
            'email' => 'etudiant2@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ETU-002',
            'department_id' => $genieLogiciel->id,
            'email_verified_at' => now(),
        ]);
        $etudiant2->assignRole('Etudiant');

        // Étudiant — Réseaux
        $etudiant3 = User::create([
            'name' => 'Paul Tshimanga',
            'email' => 'etudiant3@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ETU-003',
            'department_id' => $reseaux->id,
            'email_verified_at' => now(),
        ]);
        $etudiant3->assignRole('Etudiant');

        // Étudiant — ECOPO
        $etudiant4 = User::create([
            'name' => 'Esther Ilunga',
            'email' => 'etudiant4@udbl-tfc.cd',
            'password' => Hash::make('password'),
            'matricule' => 'ETU-004',
            'department_id' => $gestionEntreprise->id,
            'email_verified_at' => now(),
        ]);
        $etudiant4->assignRole('Etudiant');
    }
}
