<?php

namespace Database\Seeders\testdata;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomTestAccountsSeeder extends Seeder
{
    public function run()
    {
        // Création d'un super administrateur
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'commission_rate' => 0,
            'photo_path' => 'profiles/admin.jpg',
        ]);

        // Création d'un directeur régional
        $regionalManager = User::create([
            'name' => 'Directeur Régional',
            'email' => 'directeur@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'manager',
            'commission_rate' => 5.0,
            'photo_path' => 'profiles/manager1.jpg',
        ]);

        // Création d'un manager de boutique avec performances exceptionnelles
        $topManager = User::create([
            'name' => 'Manager Premium',
            'email' => 'manager-premium@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'manager',
            'commission_rate' => 7.5,
            'photo_path' => 'profiles/manager2.jpg',
        ]);

        // Création d'un manager de boutique standard
        $standardManager = User::create([
            'name' => 'Manager Standard',
            'email' => 'manager-standard@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'manager',
            'commission_rate' => 3.0,
            'photo_path' => 'profiles/manager3.jpg',
        ]);

        // Création d'un vendeur senior avec performances exceptionnelles
        $seniorVendor = User::create([
            'name' => 'Vendeur Senior',
            'email' => 'vendeur-senior@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'vendeur',
            'commission_rate' => 6.0,
            'photo_path' => 'profiles/vendor1.jpg',
        ]);

        // Création d'un vendeur standard
        $standardVendor = User::create([
            'name' => 'Vendeur Standard',
            'email' => 'vendeur-standard@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'vendeur',
            'commission_rate' => 4.0,
            'photo_path' => 'profiles/vendor2.jpg',
        ]);

        // Création d'un vendeur junior (nouvel employé)
        $juniorVendor = User::create([
            'name' => 'Vendeur Junior',
            'email' => 'vendeur-junior@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123!'),
            'remember_token' => Str::random(10),
            'role' => 'vendeur',
            'commission_rate' => 2.5,
            'photo_path' => 'profiles/vendor3.jpg',
        ]);

        // Trouver les boutiques existantes
        $shops = Shop::all();

        if ($shops->count() >= 3) {
            // Assigner les managers aux boutiques
            $regionalManager->shops()->attach($shops->pluck('id')->toArray(), ['is_manager' => true, 'assigned_at' => now()]);
            $topManager->shops()->attach($shops[0]->id, ['is_manager' => true, 'assigned_at' => now()]);
            $standardManager->shops()->attach($shops[1]->id, ['is_manager' => true, 'assigned_at' => now()]);

            // Assigner les vendeurs aux boutiques
            $seniorVendor->shops()->attach([$shops[0]->id, $shops[1]->id], ['assigned_at' => now()]);
            $standardVendor->shops()->attach($shops[1]->id, ['assigned_at' => now()]);
            $juniorVendor->shops()->attach($shops[2]->id, ['assigned_at' => now()]);

            // Personnaliser certains taux de commission
            $seniorVendor->shops()->updateExistingPivot($shops[0]->id, ['custom_commission_rate' => 8.0]);
            $seniorVendor->shops()->updateExistingPivot($shops[1]->id, ['custom_commission_rate' => 5.5]);
        }

        echo "Des comptes utilisateurs de test avec différents rôles ont été créés avec succès!\n";
        echo "--------------------------------------------------------------------\n";
        echo "Super Admin: superadmin@test.com / test123!\n";
        echo "Directeur Régional: directeur@test.com / test123!\n";
        echo "Manager Premium: manager-premium@test.com / test123!\n";
        echo "Manager Standard: manager-standard@test.com / test123!\n";
        echo "Vendeur Senior: vendeur-senior@test.com / test123!\n";
        echo "Vendeur Standard: vendeur-standard@test.com / test123!\n";
        echo "Vendeur Junior: vendeur-junior@test.com / test123!\n";
        echo "--------------------------------------------------------------------\n";
    }
}
