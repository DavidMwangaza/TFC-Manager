<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->float('similarity_score')->nullable()->after('status')
                  ->comment('Score TF-IDF Cosinus par rapport aux anciens sujets');
            $table->json('similarity_details')->nullable()->after('similarity_score')
                  ->comment('IDs des sujets similaires et leurs scores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['similarity_score', 'similarity_details']);
        });
    }
};
