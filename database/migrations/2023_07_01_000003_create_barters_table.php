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
        Schema::create('barters', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Créateur
            $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null'); // Vendeur
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('same_type'); // same_type, different_type
            $table->decimal('value_given', 12, 2); // Valeur des biens donnés par le client
            $table->decimal('value_received', 12, 2); // Valeur des biens reçus par le client
            $table->decimal('additional_payment', 12, 2)->default(0); // Paiement supplémentaire si nécessaire
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->timestamps();
        });

        Schema::create('barter_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('description')->nullable();
            $table->string('type')->default('given'); // given, received
            $table->timestamps();
        });

        Schema::create('barter_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('given'); // given, received
            $table->decimal('value', 12, 2);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_items');
        Schema::dropIfExists('barter_images');
        Schema::dropIfExists('barters');
    }
}; 