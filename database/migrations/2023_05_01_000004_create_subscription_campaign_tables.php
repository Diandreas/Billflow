<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables plans d'abonnement et campagnes marketing
     */
    public function up(): void
    {
        // Fonctionnalités (features)
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        // Plans d'abonnement
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->integer('max_clients');
            $table->integer('campaigns_per_cycle');
            $table->integer('sms_quota');
            $table->integer('sms_personal_quota');
            $table->integer('sms_rollover_percent')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Table pivot entre plans d'abonnement et fonctionnalités
        Schema::create('subscription_plan_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->timestamps();
            
            $table->unique(['subscription_plan_id', 'feature_id']);
        });

        // Abonnements utilisateurs
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

        // Campagnes marketing
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('message');
            $table->enum('type', ['birthday', 'holiday', 'promotion', 'custom']);
            $table->enum('status', ['draft', 'scheduled', 'sent', 'cancelled'])->default('draft');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->integer('sms_count')->default(0);
            $table->integer('sms_sent')->default(0);
            $table->integer('sms_delivered')->default(0);
            $table->json('target_segments')->nullable();
            $table->timestamps();
        });

        // Messages promotionnels
        Schema::create('promotional_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->string('phone_number');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->json('delivery_data')->nullable();
            $table->string('message_id')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotional_messages');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plan_feature');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('features');
    }
}; 