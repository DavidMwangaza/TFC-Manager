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
            $table->foreignId('bat_signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('bat_signed_at')->nullable();
            $table->string('bat_signature_hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['bat_signed_by']);
            $table->dropColumn(['bat_signed_by', 'bat_signed_at', 'bat_signature_hash']);
        });
    }
};
