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
            $table->string('name')->unique();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();

            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Table pivot user-shop pour les vendeurs et managers
        Schema::create('shop_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_manager')->default(false)->comment('Indique si l\'utilisateur est gérant de cette boutique');
            $table->decimal('custom_commission_rate', 5, 2)->nullable()->comment('Taux de commission spécifique pour cette boutique, remplace celui du vendeur si présent');
            $table->date('assigned_at')->default(now());
            $table->timestamps();
            $table->unique(['shop_id', 'user_id']);
        });

        // Modification de la table bills pour ajouter la référence à la boutique et au vendeur
        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->constrained()->after('client_id');
            $table->foreignId('seller_id')->nullable()->after('shop_id')->comment('L\'ID du vendeur qui a effectué la vente');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->string('signature_path')->nullable()->comment('Chemin du fichier de la signature');
            $table->integer('reprint_count')->default(0)->comment('Nombre de réimpressions');
            $table->timestamp('last_print_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['shop_id', 'seller_id', 'signature_path', 'reprint_count', 'last_print_date']);
        });
        
        Schema::dropIfExists('shop_user');
        Schema::dropIfExists('shops');
    }
}; 