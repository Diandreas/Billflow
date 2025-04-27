<?php

namespace Database\Seeders\testdata;

use App\Models\Shop;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAndShopSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');

        // Création d'un utilisateur administrateur principal
        $admin = User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@billflow.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'commission_rate' => 0, // Les admins n'ont pas de commission
        ]);

        $this->command->info('Admin principal créé : admin@billflow.com');

        // Création d'un second administrateur
        $admin2 = User::create([
            'name' => 'Directeur Général',
            'email' => 'dg@billflow.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'commission_rate' => 1.5, // Commission spéciale pour le DG
        ]);

        $this->command->info('Second admin créé : dg@billflow.com');

        // Création de 5 boutiques avec des emplacements diversifiés
        $shopData = [
            [
                'name' => 'BillFlow Central',
                'address' => '123 Avenue du Commerce, Douala',
                'city' => 'Douala',
                'region' => 'Littoral'
            ],
            [
                'name' => 'BillFlow Yaoundé',
                'address' => '45 Boulevard des Ministères, Yaoundé',
                'city' => 'Yaoundé',
                'region' => 'Centre'
            ],
            [
                'name' => 'BillFlow Ouest',
                'address' => '78 Rue du Marché, Bafoussam',
                'city' => 'Bafoussam',
                'region' => 'Ouest'
            ],
            [
                'name' => 'BillFlow Littoral',
                'address' => '10 Avenue de la Plage, Kribi',
                'city' => 'Kribi',
                'region' => 'Sud'
            ],
            [
                'name' => 'BillFlow Nord',
                'address' => '55 Rue des Artisans, Garoua',
                'city' => 'Garoua',
                'region' => 'Nord'
            ],
        ];

        $shops = [];
        foreach ($shopData as $data) {
            $shop = Shop::create([
                'name' => $data['name'],
                'address' => $data['address'],
                'city' => $data['city'],
                'region' => $data['region'],
                'phone' => '+237 ' . $faker->numberBetween(6, 7) . $faker->numerify('########'),
                'email' => strtolower(str_replace(' ', '', $data['name'])) . '@billflow.com',
                'description' => 'Boutique ' . $data['name'] . ' spécialisée dans la vente et les services numériques.',
                'is_active' => true,
                'created_at' => $faker->dateTimeBetween('-2 years', '-6 months'),
            ]);
            $shops[] = $shop;
            $this->command->info('Boutique créée : ' . $shop->name);
        }

        // Création de 5 managers (un par boutique)
        $managers = [];
        for ($i = 0; $i < 5; $i++) {
            $manager = User::create([
                'name' => 'Manager ' . ($i + 1) . ' ' . $faker->lastName,
                'email' => 'manager' . ($i + 1) . '@billflow.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'manager',
                'commission_rate' => $faker->randomFloat(1, 2.0, 3.0), // 2% à 3% de commission
                'created_at' => $faker->dateTimeBetween('-2 years', '-1 year'),
            ]);
            $managers[] = $manager;
            $this->command->info('Manager créé : ' . $manager->email);
        }

        // Assigner les managers aux boutiques (un manager par boutique)
        foreach ($managers as $index => $manager) {
            $manager->shops()->attach($shops[$index]->id, [
                'is_manager' => true,
                'assigned_at' => $faker->dateTimeBetween('-1 year', '-6 months')
            ]);
        }

        // Création de 15 vendeurs avec des statistiques variées
        $vendeurs = [];
        $commissionRates = [3.0, 3.5, 4.0, 4.5, 5.0, 3.0, 3.5, 4.0, 4.5, 5.0, 3.0, 3.5, 4.0, 4.5, 5.0];

        for ($i = 0; $i < 15; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $gender === 'male' ? $faker->firstNameMale : $faker->firstNameFemale;

            $vendeur = User::create([
                'name' => $firstName . ' ' . $faker->lastName,
                'email' => 'vendeur' . ($i + 1) . '@billflow.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'vendeur',
                'commission_rate' => $commissionRates[$i],
                'phone' => '+237 ' . $faker->numberBetween(6, 7) . $faker->numerify('########'),
                'address' => $faker->streetAddress . ', ' . $faker->city,
                'created_at' => $faker->dateTimeBetween('-18 months', '-3 months'),
            ]);
            $vendeurs[] = $vendeur;
            $this->command->info('Vendeur créé : ' . $vendeur->email . ' (commission: ' . $vendeur->commission_rate . '%)');
        }

        // Assigner les vendeurs aux boutiques (3 vendeurs par boutique)
        foreach ($shops as $index => $shop) {
            $shopVendeurs = array_slice($vendeurs, $index * 3, 3);
            foreach ($shopVendeurs as $vendeur) {
                $vendeur->shops()->attach($shop->id, [
                    'is_manager' => false,
                    'assigned_at' => $faker->dateTimeBetween('-1 year', '-1 month')
                ]);
            }
            $this->command->info('Assignation de 3 vendeurs à la boutique ' . $shop->name);
        }

        // Certains vendeurs travaillent dans plusieurs boutiques
        // Vendeurs polyvalents qui travaillent dans 2 boutiques
        for ($i = 0; $i < 5; $i++) {
            $vendeur = $vendeurs[$i];
            $currentShops = $vendeur->shops->pluck('id')->toArray();

            // Trouver une boutique où ce vendeur ne travaille pas encore
            $availableShops = Shop::whereNotIn('id', $currentShops)->pluck('id');
            if ($availableShops->isNotEmpty()) {
                $newShopId = $availableShops->random();
                $vendeur->shops()->attach($newShopId, [
                    'is_manager' => false,
                    'assigned_at' => $faker->dateTimeBetween('-6 months', '-1 week'),
                    'custom_commission_rate' => $faker->randomFloat(1, 4.5, 6.0) // Commission spéciale pour la seconde boutique
                ]);
                $this->command->info('Vendeur ' . $vendeur->name . ' assigné à une boutique supplémentaire avec commission spéciale');
            }
        }


        $this->command->info('Création des utilisateurs et boutiques terminée !');

        // Correction du problème avec les comptes utilisant un rôle invalide (staff)
        $this->command->info('NOTE: Le rôle "staff" n\'existe pas dans l\'énumération de la table users.');
        $this->command->info('Les rôles valides sont: admin, manager, vendeur');

        // Créer le compte de support avec un rôle valide
        $supportUser = User::create([
            'name' => 'Thérèse Bruneau',
            'email' => 'support.technique@billflow.com',
            'email_verified_at' => now()->addYear(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'role' => 'manager', // Utiliser un rôle valide (admin, manager, vendeur)
            'commission_rate' => 0,
            'created_at' => now()->subMonths(8),
            'updated_at' => now()->addYear(),
        ]);

        $this->command->info('Compte de support créé : support.technique@billflow.com avec le rôle "manager"');
    }
}
