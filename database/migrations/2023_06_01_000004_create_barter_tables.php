<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table principale des trocs
        Schema::create('barters', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('payment_method')->nullable();
            $table->integer('bill_id')->nullable();

            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->foreignId('shop_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Utilisateur qui a enregistré le troc');
            $table->foreignId('seller_id')->nullable()->comment('Vendeur qui a effectué le troc');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
            $table->decimal('given_items_value', 12, 2)->default(0)->comment('Valeur estimée des articles donnés par le client');
            $table->decimal('received_items_value', 12, 2)->default(0)->comment('Valeur des articles reçus par le client');
            $table->decimal('balance_amount', 12, 2)->default(0)->comment('Montant de l\'équilibrage (positif si le client paie, négatif s\'il reçoit)');
            $table->enum('status', ['en_attente', 'completed', 'annulé' ,'pending'])->default('en_attente');
            // $table->date('date');
            $table->decimal('additional_payment', 12, 2)->default(0)->comment('Montant de l\'équilibrage (positif si le client paie, négatif s\'il reçoit)');

            $table->text('notes')->nullable();
            $table->string('signature_path')->nullable()->comment('Chemin du fichier de la signature');
            $table->timestamps();
        });

        // Table pour les éléments donnés par le client
        Schema::create('barter_given_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('estimated_value', 12, 2);
            $table->json('images')->nullable()->comment('Tableau de chemins d\'images');
            $table->enum('condition', ['neuf', 'comme_neuf', 'bon', 'acceptable', 'mauvais'])->default('bon');
            $table->enum('type', ['produit_existant', 'autre'])->default('autre');
            $table->foreignId('product_id')->nullable()->comment('Référence à un produit existant si applicable');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->timestamps();
        });

        // Table pour les éléments reçus par le client (produits du stock)
        Schema::create('barter_received_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });

        // Ajout de la clé étrangère pour barter_id dans la table commissions
        Schema::table('commissions', function (Blueprint $table) {
            $table->foreign('barter_id')->references('id')->on('barters')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropForeign(['barter_id']);
        });

        Schema::dropIfExists('barter_received_items');
        Schema::dropIfExists('barter_given_items');
        Schema::dropIfExists('barters');
    }
};
