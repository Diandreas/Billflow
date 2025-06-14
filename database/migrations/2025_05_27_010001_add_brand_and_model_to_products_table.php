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
            $table->unsignedBigInteger('brand_id')->nullable()->after('supplier_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            
            $table->unsignedBigInteger('product_model_id')->nullable()->after('brand_id');
            $table->foreign('product_model_id')->references('id')->on('product_models')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
            
            $table->dropForeign(['product_model_id']);
            $table->dropColumn('product_model_id');
        });
    }
};
