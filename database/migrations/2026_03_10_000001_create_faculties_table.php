<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Créer la table des facultés
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Ajouter la FK faculty_id sur departments
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('faculty_id')->nullable()->after('id')->constrained('faculties')->nullOnDelete();
        });

        // 3. Migrer les données existantes : chaque valeur distincte de 'faculty' → une ligne dans 'faculties'
        $existingFaculties = DB::table('departments')->distinct()->pluck('faculty')->filter();
        foreach ($existingFaculties as $facultyName) {
            $facultyId = DB::table('faculties')->insertGetId([
                'name' => $facultyName,
                'code' => $facultyName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('departments')
                ->where('faculty', $facultyName)
                ->update(['faculty_id' => $facultyId]);
        }

        // 4. Supprimer l'ancienne colonne string 'faculty'
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('faculty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('faculty')->nullable()->after('id');
        });

        // Re-migrer les données
        $faculties = DB::table('faculties')->get();
        foreach ($faculties as $faculty) {
            DB::table('departments')
                ->where('faculty_id', $faculty->id)
                ->update(['faculty' => $faculty->name]);
        }

        Schema::table('departments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('faculty_id');
        });

        Schema::dropIfExists('faculties');
    }
};
