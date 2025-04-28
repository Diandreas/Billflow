<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barters', function (Blueprint $table) {
            // Add bill_id column if it doesn't exist
            if (!Schema::hasColumn('barters', 'bill_id')) {
                $table->unsignedBigInteger('bill_id')->nullable()->after('status');
                $table->foreign('bill_id')->references('id')->on('bills')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barters', function (Blueprint $table) {
            // Check if the column exists before trying to drop it
            if (Schema::hasColumn('barters', 'bill_id')) {
                $table->dropForeign(['bill_id']);
                $table->dropColumn('bill_id');
            }
        });
    }
}; 