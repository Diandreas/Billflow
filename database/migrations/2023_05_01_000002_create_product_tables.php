<?php
// 2023_05_01_000002_create_product_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catégories de produits
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('accounting_code')->nullable()->comment('Code comptable pour cette catégorie');
            $table->string('tax_code')->nullable()->comment('Code fiscal pour cette catégorie');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->timestamps();
        });

        // Produits
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('default_price', 12, 2)->nullable();
            $table->string('type')->nullable()->comment('Type de produit: physique, service, numérique, etc.');
            $table->string('sku')->nullable()->comment('Référence unique du produit');
            $table->integer('stock_quantity')->default(0)->nullable()->comment('Quantité en stock');
            $table->integer('stock_alert_threshold')->nullable()->comment('Seuil d\'alerte de stock');
            $table->string('accounting_category')->nullable()->comment('Catégorie comptable');
            $table->string('tax_category')->nullable()->comment('Catégorie fiscale');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->decimal('cost_price', 10, 2)->nullable()->comment('Prix d\'achat');
            $table->string('status')->default('actif')->nullable()->comment('Statut: actif, inactif');
            $table->boolean('is_barterable')->default(false)->nullable()->comment('Indique si le produit peut être utilisé dans un troc');
            $table->decimal('total_sales', 12, 2)->default(0)->nullable()->comment('Total des ventes de ce produit');
            $table->timestamps();
        });

        // Table des mouvements d'inventaire
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullable();
            $table->string('type')->nullable(); // entrée, sortie, ajustement, vente, achat
            $table->integer('quantity')->nullable();
            $table->string('reference')->nullable(); // référence externe
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->integer('stock_before')->nullable();
            $table->integer('stock_after')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
