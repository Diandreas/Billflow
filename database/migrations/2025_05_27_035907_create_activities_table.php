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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action')->nullable()->comment('Type d\'action: create, update, delete, login, logout, etc.');
            $table->string('model_type')->nullable()->comment('Type de modèle concerné: Client, Bill, Product, etc.');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID de l\'élément concerné');
            $table->json('old_values')->nullable()->comment('Anciennes valeurs en cas de modification');
            $table->json('new_values')->nullable()->comment('Nouvelles valeurs');
            $table->text('description')->nullable()->comment('Description de l\'action');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->timestamps();
            
            // Index pour améliorer les performances des requêtes
            $table->index(['model_type', 'model_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
