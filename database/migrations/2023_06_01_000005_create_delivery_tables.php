<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table principale des livraisons
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('barter_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('delivery_agent_id')->nullable()->comment('Agent de livraison (lié à users)');
            $table->foreign('delivery_agent_id')->references('id')->on('users')->onDelete('set null');
            $table->string('client_name');
            $table->string('client_phone');
            $table->text('delivery_address');
            $table->text('delivery_notes')->nullable();
            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('delivered_date')->nullable();
            $table->string('status')->default('pending');
            $table->string('tracking_number')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamps();
        });

        // Table pour le suivi des statuts des livraisons
        Schema::create('delivery_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_status_logs');
        Schema::dropIfExists('deliveries');
    }
}; 