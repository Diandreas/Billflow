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
        Schema::table('commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('commissions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_at');
            }
            
            if (!Schema::hasColumn('commissions', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('commissions', 'payment_group_id')) {
                $table->unsignedBigInteger('payment_group_id')->nullable()->after('payment_reference')
                      ->comment('ID pour grouper plusieurs paiements ensemble');
            }
            
            if (!Schema::hasColumn('commissions', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_group_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to remove columns in down() as they might contain data
    }
}; 