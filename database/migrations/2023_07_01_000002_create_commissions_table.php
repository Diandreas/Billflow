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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('vente'); // vente, surplus, performance, etc.
            $table->decimal('amount', 12, 2);
            $table->decimal('rate', 5, 2)->nullable();
            $table->decimal('base_amount', 12, 2)->nullable(); // Montant sur lequel la commission est calculée
            $table->text('description')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
}; 