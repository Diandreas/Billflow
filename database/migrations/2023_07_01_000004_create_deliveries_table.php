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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Créateur
            $table->foreignId('delivery_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('delivery_address');
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('status')->default('pending'); // pending, in_transit, delivered, cancelled
            $table->text('notes')->nullable();
            $table->string('payment_status')->default('unpaid'); // unpaid, paid
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
}; 