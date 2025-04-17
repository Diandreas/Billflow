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
        Schema::create('vendor_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->string('serial_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('assigned_date');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->string('condition');
            $table->text('notes')->nullable();
            $table->enum('status', ['assigned', 'returned'])->default('assigned');
            $table->date('returned_date')->nullable();
            $table->string('return_condition')->nullable();
            $table->text('return_notes')->nullable();
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_equipment');
    }
}; 