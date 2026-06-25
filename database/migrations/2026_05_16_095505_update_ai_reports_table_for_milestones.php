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
        Schema::table('ai_reports', function (Blueprint $table) {
            $table->foreignId('thesis_file_id')->nullable()->change();
            $table->foreignId('milestone_id')->nullable()->constrained('milestones')->cascadeOnDelete()->after('thesis_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_reports', function (Blueprint $table) {
            $table->foreignId('thesis_file_id')->nullable(false)->change();
            $table->dropConstrainedForeignId('milestone_id');
        });
    }
};
