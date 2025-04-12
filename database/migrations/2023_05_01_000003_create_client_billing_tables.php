<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables clients et facturation
     */
    public function up(): void
    {
        // Table des téléphones
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->timestamps();
        });

        // Table des clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->enum('sex', ['M', 'F', 'Other'])->nullable();
            $table->date('birth')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Table pivot client-téléphone
        Schema::create('client_phone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('phone_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['client_id', 'phone_id']);
        });

        // Table des factures
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->decimal('total', 12, 2);
            $table->dateTime('date');
            $table->dateTime('due_date')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(19.25);
            $table->decimal('tax_amount', 12, 2);
            $table->string('status')->default('En attente');
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });

        // Table pivot facture-produit
        Schema::create('bill_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        // Table des mouvements d'inventaire
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('type'); // entrée, sortie, ajustement, vente, achat
            $table->integer('quantity');
            $table->string('reference')->nullable(); // référence externe
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('bill_products');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('client_phone');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('phones');
    }
}; 