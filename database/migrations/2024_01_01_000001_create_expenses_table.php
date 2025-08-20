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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->string('category');
            $table->string('supplier')->nullable();
            $table->string('payment_method');
            $table->text('description')->nullable();
            $table->string('receipt_path')->nullable(); // Chemin vers le justificatif photo
            $table->json('receipt_data')->nullable(); // Données extraites par OCR
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable(); // monthly, weekly, yearly
            $table->date('next_occurrence')->nullable();
            $table->timestamps();

            $table->index(['shop_id', 'date']);
            $table->index(['category', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};