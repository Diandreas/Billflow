<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Vendeur qui reçoit la commission');
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null')->comment('Facture liée à cette commission');
            $table->foreignId('barter_id')->nullable()->comment('Troc lié à cette commission');
            $table->foreignId('shop_id')->constrained()->comment('Boutique où la vente a été effectuée');
            $table->decimal('amount', 10, 2)->comment('Montant de la commission');
            $table->decimal('rate', 5, 2)->comment('Taux appliqué pour cette commission');
            $table->decimal('base_amount', 10, 2)->comment('Montant sur lequel la commission a été calculée');
            $table->enum('type', ['vente', 'troc', 'surplus'])->default('vente')->comment('Type de commission');
            $table->text('description')->nullable()->comment('Description ou détails supplémentaires');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable()->comment('Date de paiement de la commission');
            $table->foreignId('paid_by')->nullable()->comment('Utilisateur qui a effectué le paiement');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });

        // Créer une contrainte de clé étrangère pour barter_id quand la table sera créée plus tard
        // Nous allons créer la table barters dans une migration ultérieure
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
}; 