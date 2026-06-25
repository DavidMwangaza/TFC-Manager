<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            // Type de soumission : 'pdf' (upload fichier) ou 'text' (rédaction en ligne)
            $table->enum('submission_type', ['pdf', 'text'])->nullable()->after('submission_date');

            // Contenu rédigé par l'étudiant en ligne (texte enrichi HTML)
            $table->longText('submitted_text')->nullable()->after('submission_type');

            // Contenu corrigé par le professeur (texte enrichi HTML)
            $table->longText('corrected_text')->nullable()->after('submitted_text');

            // Référence au fichier PDF annoté généré par le professeur
            $table->foreignId('annotated_file_id')->nullable()->constrained('thesis_files')->nullOnDelete()->after('corrected_text');
        });
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropForeign(['annotated_file_id']);
            $table->dropColumn(['submission_type', 'submitted_text', 'corrected_text', 'annotated_file_id']);
        });
    }
};
