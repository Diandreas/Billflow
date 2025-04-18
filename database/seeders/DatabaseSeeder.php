<?php

namespace Database\Seeders;

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
        // Désactiver les vérifications de clés étrangères pour faciliter la suppression
        Schema::disableForeignKeyConstraints();
        
        // Supprimer toutes les données des tables principales
        $this->truncateTables([
            'users',
            'shops',
            'clients',
            'phones', 
            'client_phone',
            'products',
            'product_categories',
            'bills',
            'bill_products',
            'inventory_movements',
            'commissions',
            'vendor_equipment',
            'promotional_messages',
            'campaigns',
            'settings',
            'subscription_plans',
            'subscriptions',
            'features',
            'barters',
            'barter_given_items',
            'barter_received_items',
            'deliveries',
            'delivery_status_logs'
        ]);
        
        // Réactiver les vérifications de clés étrangères
        Schema::enableForeignKeyConstraints();
        
        // Seeder les données dans l'ordre des dépendances
        $this->call([
            SettingsSeeder::class,
            UserAndShopSeeder::class,
            PhonesSeeder::class,
            ClientsSeeder::class,
            ProductsSeeder::class,
            InventoryMovementSeeder::class,
            BillsSeeder::class,
            VendorEquipmentSeeder::class,
            SubscriptionPlanSeeder::class,
            // Autres seeders comme nécessaire
        ]);
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
