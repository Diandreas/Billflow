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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('business_type')->default('boutique'); // boutique, prestataire, restaurant, eleveur, agriculteur, transport
            $table->json('profile_config')->nullable(); // Configuration spécifique au profil
            $table->json('active_modules')->nullable(); // Modules activés
            $table->json('custom_labels')->nullable(); // Libellés personnalisés
            $table->json('custom_units')->nullable(); // Unités personnalisées
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'profile_config', 'active_modules', 'custom_labels', 'custom_units']);
        });
    }
};