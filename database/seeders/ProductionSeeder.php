<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application for production environment.
     * Creates basic structure with default users, shop, and settings.
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
}
