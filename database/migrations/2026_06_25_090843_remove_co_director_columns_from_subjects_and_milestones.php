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
            $table->dropForeign(['co_director_id']);
            $table->dropColumn('co_director_id');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->dropForeign(['annotated_file_id']);
            $table->dropColumn([
                'co_validated_at',
                'co_director_comments',
                'submission_type',
                'submitted_text',
                'corrected_text',
                'annotated_file_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('co_director_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->timestamp('co_validated_at')->nullable();
            $table->text('co_director_comments')->nullable();
            $table->enum('submission_type', ['pdf', 'text'])->nullable();
            $table->longText('submitted_text')->nullable();
            $table->longText('corrected_text')->nullable();
            $table->foreignId('annotated_file_id')->nullable()->constrained('thesis_files')->nullOnDelete();
        });
    }
};
