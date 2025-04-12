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
        Schema::table('products', function (Blueprint $table) {
            $table->string('type')->nullable()->after('description')->comment('Type de produit: physique, service, numérique, etc.');
            $table->string('sku')->nullable()->after('type')->comment('Référence unique du produit');
            $table->integer('stock_quantity')->default(0)->after('sku')->comment('Quantité en stock');
            $table->integer('stock_alert_threshold')->nullable()->after('stock_quantity')->comment('Seuil d\'alerte de stock');
            $table->string('accounting_category')->nullable()->after('stock_alert_threshold')->comment('Catégorie comptable');
            $table->string('tax_category')->nullable()->after('accounting_category')->comment('Catégorie fiscale');
            $table->decimal('cost_price', 10, 2)->nullable()->after('tax_category')->comment('Prix d\'achat');
            $table->string('status')->default('actif')->after('cost_price')->comment('Statut: actif, inactif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'sku',
                'stock_quantity',
                'stock_alert_threshold',
                'accounting_category',
                'tax_category',
                'cost_price',
                'status'
            ]);
        });
    }
};
