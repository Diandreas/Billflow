<?php

namespace Database\Seeders;

use Database\Seeders\testdata\BillsSeeder;
use Database\Seeders\testdata\ClientsSeeder;
use Database\Seeders\testdata\InventoryMovementSeeder;
use Database\Seeders\testdata\PhonesSeeder;
use Database\Seeders\testdata\ProductsSeeder;
use Database\Seeders\testdata\SubscriptionPlanSeeder;
use Database\Seeders\testdata\UserAndShopSeeder;
use Database\Seeders\testdata\VendorEquipmentSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clés étrangères pour le seeding
        Schema::disableForeignKeyConstraints();

        // Exécuter les seeders de base
        $this->call([
            SettingsSeeder::class,
            BasicShopSeeder::class,
            BasicUserSeeder::class,
            BasicProductCategorySeeder::class,
        ]);

        // Réactiver les contraintes de clés étrangères
        Schema::enableForeignKeyConstraints();

        $this->command->info('Application initialisée avec succès pour l\'environnement de production.');
        $this->command->info('Utilisateurs créés:');
        $this->command->info('- Admin: admin@billflow.com / admin123');
        $this->command->info('- Manager: manager@billflow.com / manager123');
        $this->command->info('- Vendeur: vendeur@billflow.com / vendeur123');
    }


    /**
     * Vide complètement les tables spécifiées
     */
    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Table {$table} vidée avec succès");
            } else {
                $this->command->warn("Table {$table} n'existe pas");
            }
        }
    }
}
