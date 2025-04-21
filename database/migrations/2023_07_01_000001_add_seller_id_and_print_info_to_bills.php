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
        Schema::table('bills', function (Blueprint $table) {
            // Ajout de la colonne seller_id pour différencier le créateur et le vendeur
            $table->foreignId('seller_id')->nullable()->after('user_id')->references('id')->on('users');
            
            // Ajout des informations de suivi d'impression
            $table->integer('print_count')->default(0)->after('comments');
            $table->dateTime('last_printed_at')->nullable()->after('print_count');
            
            // Ajout du flag d'approbation pour les prix réduits
            $table->boolean('needs_approval')->default(false)->after('status');
            $table->boolean('approved')->default(false)->after('needs_approval');
            $table->foreignId('approved_by')->nullable()->after('approved')->references('id')->on('users');
            $table->dateTime('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'seller_id',
                'print_count',
                'last_printed_at',
                'needs_approval',
                'approved',
                'approved_by',
                'approved_at',
                'approval_notes'
            ]);
        });
    }
}; 