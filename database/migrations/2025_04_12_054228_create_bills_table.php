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

        // Table pivot pour la relation many-to-many entre factures et produits
        Schema::create('bill_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_products');
        Schema::dropIfExists('bills');
    }
};
