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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('siret')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(19.25);
            $table->string('currency')->default('XAF');
            $table->string('logo_path')->nullable();
            $table->string('invoice_prefix')->default('FACT-');
            $table->text('invoice_footer')->nullable();
            $table->integer('default_payment_terms')->default(30);
            $table->integer('default_due_days')->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
