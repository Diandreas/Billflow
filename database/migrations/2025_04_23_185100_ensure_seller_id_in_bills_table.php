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
            if (!Schema::hasColumn('bills', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->after('user_id');
                $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('bills', 'print_count')) {
                $table->integer('print_count')->default(0)->after('signature');
            }
            
            if (!Schema::hasColumn('bills', 'last_printed_at')) {
                $table->timestamp('last_printed_at')->nullable()->after('print_count');
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