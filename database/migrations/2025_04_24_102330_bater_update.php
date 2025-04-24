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
            $table->boolean('is_barter_bill')->default(false)->after('payment_method');
            $table->unsignedBigInteger('barter_id')->nullable()->after('is_barter_bill');

            // Ajouter une clé étrangère vers la table barters
            $table->foreign('barter_id')->references('id')->on('barters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['barter_id']);
            $table->dropColumn('barter_id');
            $table->dropColumn('is_barter_bill');
        });
    }
};
