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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->boolean('is_barter_item')->default(false);

            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
