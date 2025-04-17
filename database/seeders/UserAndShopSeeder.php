<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAndShopSeeder extends Seeder
{
    public function run()
    {
        // Création d'un utilisateur administrateur
        $admin = User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@billflow.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'commission_rate' => 0, // Les admins n'ont pas de commission
        ]);

        // Création de 3 boutiques
        $shops = [];
        $shopNames = ['BillFlow Central', 'BillFlow Nord', 'BillFlow Sud'];
        
        foreach ($shopNames as $index => $name) {
            $shops[] = Shop::create([
                'name' => $name,
                'address' => '123 Rue du Commerce ' . ($index + 1),
                'phone' => '01234' . $index . '6789',
                'email' => 'boutique' . ($index + 1) . '@billflow.com',
                'description' => 'Boutique ' . $name . ' spécialisée dans la vente de produits haut de gamme.',
                'is_active' => true,
            ]);
        }

        // Création de 2 managers (un pour chaque boutique)
        $managers = [];
        for ($i = 0; $i < 2; $i++) {
            $managers[] = User::create([
                'name' => 'Manager ' . ($i + 1),
                'email' => 'manager' . ($i + 1) . '@billflow.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'manager',
                'commission_rate' => 2.5, // 2.5% de commission de base
            ]);
        }

        // Assigner les managers aux boutiques
        $managers[0]->shops()->attach($shops[0]->id, ['is_manager' => true, 'assigned_at' => now()]);
        $managers[0]->shops()->attach($shops[1]->id, ['is_manager' => true, 'assigned_at' => now()]);
        $managers[1]->shops()->attach($shops[2]->id, ['is_manager' => true, 'assigned_at' => now()]);

        // Création de 6 vendeurs
        $commissionRates = [3.0, 3.5, 4.0, 3.0, 3.5, 4.0]; // Taux de commission variés
        $vendors = [];
        
        for ($i = 0; $i < 6; $i++) {
            $vendors[] = User::create([
                'name' => 'Vendeur ' . ($i + 1),
                'email' => 'vendeur' . ($i + 1) . '@billflow.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'vendeur',
                'commission_rate' => $commissionRates[$i],
            ]);
        }

        // Assigner les vendeurs aux boutiques
        // 2 vendeurs par boutique
        $vendors[0]->shops()->attach($shops[0]->id, ['assigned_at' => now()]);
        $vendors[1]->shops()->attach($shops[0]->id, ['assigned_at' => now()]);
        
        $vendors[2]->shops()->attach($shops[1]->id, ['assigned_at' => now()]);
        $vendors[3]->shops()->attach($shops[1]->id, ['assigned_at' => now()]);
        
        $vendors[4]->shops()->attach($shops[2]->id, ['assigned_at' => now()]);
        $vendors[5]->shops()->attach($shops[2]->id, ['assigned_at' => now()]);

        // Certains vendeurs travaillent dans plusieurs boutiques
        $vendors[0]->shops()->attach($shops[1]->id, ['assigned_at' => now()->subDays(30)]);
        $vendors[3]->shops()->attach($shops[2]->id, ['assigned_at' => now()->subDays(60)]);
        
        // Personnaliser les taux de commission pour certains vendeurs dans certaines boutiques
        $vendors[0]->shops()->updateExistingPivot($shops[1]->id, ['custom_commission_rate' => 4.5]);
        $vendors[3]->shops()->updateExistingPivot($shops[2]->id, ['custom_commission_rate' => 5.0]);
    }
} 