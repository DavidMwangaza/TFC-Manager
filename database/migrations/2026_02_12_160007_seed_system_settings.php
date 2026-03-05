<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_settings')->insert([
            [
                'key' => 'subject_deadline',
                'value' => null,
                'type' => 'date',
                'group' => 'deadlines',
                'label' => 'Date limite de dépôt des sujets',
                'description' => 'Après cette date, les étudiants ne pourront plus soumettre de sujets.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'thesis_deadline',
                'value' => null,
                'type' => 'date',
                'group' => 'deadlines',
                'label' => 'Date limite de dépôt des fichiers TFC',
                'description' => 'Après cette date, les étudiants ne pourront plus déposer de fichiers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'final_deposit_deadline',
                'value' => null,
                'type' => 'date',
                'group' => 'deadlines',
                'label' => 'Date limite du dépôt final',
                'description' => 'Après cette date, le dépôt final (version définitive) sera bloqué.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ai_similarity_threshold',
                'value' => '40',
                'type' => 'integer',
                'group' => 'ai',
                'label' => 'Seuil d\'alerte IA (similarité %)',
                'description' => 'Pourcentage de similarité à partir duquel une alerte est déclenchée.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ai_score_threshold',
                'value' => '50',
                'type' => 'integer',
                'group' => 'ai',
                'label' => 'Seuil d\'alerte IA (score IA %)',
                'description' => 'Score IA à partir duquel une alerte rouge est déclenchée.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'university_name',
                'value' => 'Université Don Bosco de Lubumbashi',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nom de l\'université',
                'description' => 'Nom officiel affiché dans l\'application.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'university_acronym',
                'value' => 'UDBL',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Sigle de l\'université',
                'description' => 'Acronyme utilisé dans les en-têtes et rapports.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', [
            'subject_deadline', 'thesis_deadline', 'final_deposit_deadline',
            'ai_similarity_threshold', 'ai_score_threshold',
            'university_name', 'university_acronym',
        ])->delete();
    }
};
