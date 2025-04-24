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
        Schema::table('barters', function (Blueprint $table) {
            if (!Schema::hasColumn('barters', 'type')) {
                $table->string('type')->default('same_type')->after('shop_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barters', function (Blueprint $table) {
            if (Schema::hasColumn('barters', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
