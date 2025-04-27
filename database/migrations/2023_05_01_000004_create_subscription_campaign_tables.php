<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pivot user-shop
        Schema::create('shop_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_manager')->default(false)->nullable()->comment('Indique si l\'utilisateur est gérant de cette boutique');
            $table->decimal('custom_commission_rate', 5, 2)->nullable()->comment('Taux de commission spécifique pour cette boutique');
            $table->date('assigned_at')->default(now())->nullable();
            $table->timestamps();
            $table->unique(['shop_id', 'user_id']);
        });

        // Table des commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('barter_id')->nullable()->comment('Troc lié à cette commission');
            $table->foreignId('shop_id')->nullable()->constrained();
            $table->decimal('amount', 10, 2)->nullable()->comment('Montant de la commission');
            $table->decimal('rate', 5, 2)->nullable()->comment('Taux appliqué pour cette commission');
            $table->decimal('base_amount', 10, 2)->nullable()->comment('Montant sur lequel la commission a été calculée');
            $table->enum('type', ['vente', 'troc', 'surplus'])->default('vente')->nullable()->comment('Type de commission');
            $table->text('description')->nullable()->comment('Description ou détails supplémentaires');
            $table->boolean('is_paid')->default(false)->nullable()->comment('Indique si la commission a été payée');
            $table->date('paid_at')->nullable()->comment('Date de paiement de la commission');
            $table->foreignId('paid_by')->nullable()->comment('Utilisateur qui a effectué le paiement');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->unsignedBigInteger('payment_group_id')->nullable()->comment('ID pour grouper plusieurs paiements ensemble');
            $table->text('payment_notes')->nullable();
            $table->timestamps();
        });

        // Table des paiements de commissions
        Schema::create('commission_payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('paid_by')->nullable()->constrained('users');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Table principale des trocs
        Schema::create('barters', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('shop_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->default('same_type')->nullable();
            $table->decimal('value_given', 12, 2)->nullable()->comment('Valeur des biens donnés par le client');
            $table->decimal('value_received', 12, 2)->nullable()->comment('Valeur des biens reçus par le client');
            $table->decimal('additional_payment', 12, 2)->default(0)->nullable()->comment('Paiement supplémentaire si nécessaire');
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->text('notes')->nullable();
            $table->string('signature_path')->nullable()->comment('Chemin du fichier de la signature');
            $table->timestamps();
        });

        // Table des éléments de troc
        Schema::create('barter_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('given')->nullable();
            $table->decimal('value', 12, 2)->nullable();
            $table->integer('quantity')->default(1)->nullable();
            $table->timestamps();
        });

        // Table des images d'éléments de troc
        Schema::create('barter_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_item_id')->nullable()->constrained('barter_items')->onDelete('cascade');
            $table->string('path')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->default('image')->nullable()->comment('Type de média');
            $table->integer('order')->default(0)->nullable()->comment('Ordre d\'affichage');
            $table->timestamps();
        });

        // Table des images de troc
        Schema::create('barter_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barter_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('path')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->default('given')->nullable();
            $table->timestamps();
        });

        // Table de l'équipement vendeur
        Schema::create('vendor_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->integer('quantity')->default(1)->nullable();
            $table->date('assigned_date')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('condition')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['assigned', 'returned', 'neuf'])->default('assigned')->nullable();
            $table->date('returned_date')->nullable();
            $table->string('return_condition')->nullable();
            $table->text('return_notes')->nullable();
            $table->foreignId('returned_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_equipment');
        Schema::dropIfExists('barter_images');
        Schema::dropIfExists('barter_item_images');
        Schema::dropIfExists('barter_items');
        Schema::dropIfExists('barters');
        Schema::dropIfExists('commission_payments');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('shop_user');
    }
};
