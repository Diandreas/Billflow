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
        Schema::create('barter_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_item_id')->constrained('barter_items')->onDelete('cascade');
            $table->string('path');
            $table->string('description')->nullable();
            $table->string('type')->default('image')->comment('Type de média');
            $table->integer('order')->default(0)->comment('Ordre d\'affichage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_item_images');
    }
};
