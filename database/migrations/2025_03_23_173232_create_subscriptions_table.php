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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('restrict');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->decimal('price_paid', 10, 2);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->integer('sms_remaining')->default(0);
            $table->integer('sms_personal_remaining')->default(0);
            $table->integer('campaigns_used')->default(0);
            $table->string('transaction_reference')->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
