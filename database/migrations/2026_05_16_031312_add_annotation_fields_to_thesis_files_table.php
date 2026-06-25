<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thesis_files', function (Blueprint $table) {
            // Catégorie du fichier : 'milestone' (dépôt étudiant) ou 'annotation' (PDF annoté par prof)
            $table->string('type')->default('milestone')->after('version_type');
            // Référence à l'uploader (professeur pour les annotations)
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('thesis_files', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['type', 'uploaded_by']);
        });
    }
};
