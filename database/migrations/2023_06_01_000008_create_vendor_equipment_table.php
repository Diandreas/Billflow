<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Vendeur qui a reçu l\'équipement');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade')->comment('Boutique où l\'équipement est utilisé');
            $table->string('name')->comment('Nom de l\'équipement');
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('assigned_date');
            $table->date('return_date')->nullable();
            $table->enum('condition', ['neuf', 'bon', 'moyen', 'mauvais'])->default('bon');
            $table->string('assigned_by')->nullable()->comment('Nom de la personne qui a assigné l\'équipement');
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_equipment');
    }
}; 