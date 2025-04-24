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
            if (!Schema::hasColumn('barters', 'value_given')) {
                $table->decimal('value_given', 10, 2)->after('type')->default(0);
            }
            if (!Schema::hasColumn('barters', 'value_received')) {
                $table->decimal('value_received', 10, 2)->after('value_given')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barters', function (Blueprint $table) {
            if (Schema::hasColumn('barters', 'value_given')) {
                $table->dropColumn('value_given');
            }
            if (Schema::hasColumn('barters', 'value_received')) {
                $table->dropColumn('value_received');
            }
        });
    }
};
