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
        Schema::table('commissions', function (Blueprint $table) {
            // Ajouter la nouvelle colonne is_paid
            $table->boolean('is_paid')->default(false)->after('description')->comment('Indique si la commission a été payée');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            // Supprimer la colonne is_paid
            $table->dropColumn('is_paid');
        });
    }
};
