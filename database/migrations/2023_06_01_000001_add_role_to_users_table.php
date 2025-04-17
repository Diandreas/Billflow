<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'vendeur'])->default('vendeur')->after('remember_token');
            $table->decimal('commission_rate', 5, 2)->default(0)->comment('Taux de commission en pourcentage')->after('role');
            $table->string('photo_path')->nullable()->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'commission_rate', 'photo_path']);
        });
    }
}; 