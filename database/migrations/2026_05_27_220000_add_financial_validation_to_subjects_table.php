<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Champs de validation financière pour l'Appariteur.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('financial_status')->default('pending')->after('defense_revocation_reason');
            $table->foreignId('financial_validated_by')->nullable()->after('financial_status')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('financial_validated_at')->nullable()->after('financial_validated_by');
            $table->text('financial_notes')->nullable()->after('financial_validated_at');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['financial_validated_by']);
            $table->dropColumn([
                'financial_status',
                'financial_validated_by',
                'financial_validated_at',
                'financial_notes',
            ]);
        });
    }
};
