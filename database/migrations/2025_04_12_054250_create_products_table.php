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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('default_price', 12, 2);
            $table->string('type')->nullable()->comment('Type de produit: physique, service, numérique, etc.');
            $table->string('sku')->nullable()->comment('Référence unique du produit');
            $table->integer('stock_quantity')->default(0)->comment('Quantité en stock');
            $table->integer('stock_alert_threshold')->nullable()->comment('Seuil d\'alerte de stock');
            $table->string('accounting_category')->nullable()->comment('Catégorie comptable');
            $table->string('tax_category')->nullable()->comment('Catégorie fiscale');
            $table->decimal('cost_price', 10, 2)->nullable()->comment('Prix d\'achat');
            $table->string('status')->default('actif')->comment('Statut: actif, inactif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
