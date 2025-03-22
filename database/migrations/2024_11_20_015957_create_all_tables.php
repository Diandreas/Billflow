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
// clients table
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('sex', ['M', 'F', 'Other'])->nullable();
            $table->date('birth')->nullable();
            $table->timestamps();
        });

// phones table (renamed from tel for better convention)
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->timestamps();
        });

// client_phone pivot table
        Schema::create('client_phone', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('phone_id')->constrained()->onDelete('cascade');
            $table->primary(['client_id', 'phone_id']);
        });

// products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('default_price', 10, 2)->default(0);
            $table->timestamps();
        });

// bills table
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->date('date');
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->timestamps();
        });

// bill_product pivot table
        Schema::create('bill_products', function (Blueprint $table) {
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 10, 2);
            $table->primary(['bill_id', 'product_id']);
            $table->timestamps();
        });

// settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('siret', 14)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->string('logo_path')->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->string('invoice_prefix', 10)->default('FACT-');
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
        Schema::dropIfExists('bill_products');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('products');
        Schema::dropIfExists('client_phone');
        Schema::dropIfExists('phones');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('settings');
    }
};
