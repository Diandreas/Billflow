<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des boutiques
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            $table->timestamps();
        });

        // Table des téléphones
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique()->nullable();
            $table->timestamps();
        });

        // Table des clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->enum('sex', ['M', 'F', 'Other'])->nullable();
            $table->date('birth')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Table pivot client-téléphone
        Schema::create('client_phone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('phone_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['client_id', 'phone_id']);
        });

        // Table des factures
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->text('description')->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(19.25)->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable();
            $table->string('status')->default('En attente')->nullable();
            $table->boolean('needs_approval')->default(false)->nullable();
            $table->boolean('approved')->default(false)->nullable();
            $table->foreignId('approved_by')->nullable()->references('id')->on('users');
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('is_barter_bill')->default(false)->nullable();
            $table->unsignedBigInteger('barter_id')->nullable();
            $table->text('comments')->nullable();
            $table->integer('print_count')->default(0)->nullable();
            $table->dateTime('last_printed_at')->nullable();
            $table->boolean('is_recurring')->default(false)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('shop_id')->nullable()->constrained();
            $table->string('signature_path')->nullable()->comment('Chemin du fichier de la signature');
            $table->integer('reprint_count')->default(0)->nullable()->comment('Nombre de réimpressions');
            $table->timestamp('last_print_date')->nullable();
            $table->timestamps();
        });

        // Table pivot facture-produit
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('name')->nullable();
            $table->boolean('is_barter_item')->default(false)->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->timestamps();
        });

        // Table des livraisons
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('delivery_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0)->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_status')->default('unpaid')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('client_phone');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('phones');
        Schema::dropIfExists('shops');
    }
};
