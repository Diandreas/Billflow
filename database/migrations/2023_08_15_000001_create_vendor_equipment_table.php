<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->comment('Vendeur assigné');
            $table->foreignId('shop_id')->constrained()->comment('Boutique associée');
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('assigned_date');
            $table->foreignId('assigned_by')->constrained('users')->comment('Admin qui a assigné');
            $table->string('condition')->nullable()->comment('État à l\'attribution');
            $table->text('notes')->nullable();
            $table->enum('status', ['assigned', 'returned'])->default('assigned');
            $table->date('returned_date')->nullable();
            $table->string('return_condition')->nullable()->comment('État au retour');
            $table->text('return_notes')->nullable();
            $table->foreignId('returned_to')->nullable()->constrained('users')->comment('Admin qui a reçu le retour');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_equipment');
    }
} 