<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BasicUserSeeder extends Seeder
{
    /**
     * Crée les utilisateurs de base pour la production
     */
    public function run(): void
    {
        $this->command->info('Création des utilisateurs de base...');

        // Création de l'administrateur
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@billflow.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'commission_rate' => 0, // Pas de commission pour l'admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Création du manager
        $manager = User::create([
            'name' => 'Manager Principal',
            'email' => 'manager@billflow.com',
            'email_verified_at' => now(),
            'password' => Hash::make('manager123'),
            'remember_token' => Str::random(10),
            'role' => 'manager',
            'commission_rate' => 3.0, // 3% de commission
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Création du vendeur
        $vendeur = User::create([
            'name' => 'Vendeur Principal',
            'email' => 'vendeur@billflow.com',
            'email_verified_at' => now(),
            'password' => Hash::make('vendeur123'),
            'remember_token' => Str::random(10),
            'role' => 'vendeur',
            'commission_rate' => 5.0, // 5% de commission
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Récupération de la boutique par défaut
        $shop = Shop::first();

        if ($shop) {
            // Assigner le manager à la boutique
            $manager->shops()->attach($shop->id, [
                'is_manager' => true,
                'assigned_at' => now(),
            ]);

            // Assigner le vendeur à la boutique
            $vendeur->shops()->attach($shop->id, [
                'is_manager' => false,
                'assigned_at' => now(),
            ]);

            $this->command->info('Utilisateurs assignés à la boutique: ' . $shop->name);
        } else {
            $this->command->error('Aucune boutique trouvée. Exécutez BasicShopSeeder d\'abord.');
        }
    }
}
