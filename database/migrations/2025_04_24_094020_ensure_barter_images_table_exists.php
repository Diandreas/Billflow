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
        if (!Schema::hasTable('barter_images')) {
            Schema::create('barter_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('barter_id')->constrained()->onDelete('cascade');
                $table->string('path');
                $table->string('description')->nullable();
                $table->string('type')->default('given'); // given, received
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barter_images');
    }
};
