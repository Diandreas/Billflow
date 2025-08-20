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
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('paid_amount', 15, 2)->default(0); // Montant déjà payé
            $table->decimal('remaining_amount', 15, 2)->default(0); // Montant restant à payer
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid
            $table->foreignId('quote_id')->nullable()->constrained()->onDelete('set null'); // Devis d'origine
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'remaining_amount', 'payment_status', 'quote_id']);
        });
    }
};