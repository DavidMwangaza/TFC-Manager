<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('subject_type')->nullable()->after('title'); // tfc, memoire
            $table->text('context_relevance')->nullable()->after('description'); // Contexte et Pertinence
            $table->text('challenges')->nullable()->after('context_relevance'); // Défis et Lacunes
            $table->text('research_question')->nullable()->after('challenges'); // Question de Recherche
            $table->text('hypothesis')->nullable()->after('research_question'); // Hypothèse
            $table->text('general_objective')->nullable()->after('hypothesis'); // Objectif Général
            $table->json('specific_objectives')->nullable()->after('general_objective'); // Objectifs Spécifiques (array)
            $table->json('state_of_art')->nullable()->after('specific_objectives'); // État de l'art (tableau)
            $table->text('demarcations')->nullable()->after('state_of_art'); // Démarcations
            $table->text('methodologies')->nullable()->after('demarcations'); // Méthodologies
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'subject_type',
                'context_relevance',
                'challenges',
                'research_question',
                'hypothesis',
                'general_objective',
                'specific_objectives',
                'state_of_art',
                'demarcations',
                'methodologies',
            ]);
        });
    }
};
