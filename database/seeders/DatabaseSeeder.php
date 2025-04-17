<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seeders pour les tables de base
        $this->call([
            SettingsSeeder::class,
            ProductsSeeder::class,
        ]);
        
        // Seeders pour le système de gestion des boutiques et vendeurs
        $this->call([
            UserAndShopSeeder::class,
            VendorEquipmentSeeder::class,
        ]);
        
        // Seeders pour les données clients et ventes
        $this->call([
            ClientsSeeder::class,
            BillsSeeder::class,
            InventoryMovementSeeder::class,
            // SubscriptionPlanSeeder::class,
        ]);
    }
}
