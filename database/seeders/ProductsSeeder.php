<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Développement site web', 'description' => 'Création site web responsive', 'default_price' => 1500.00],
            ['name' => 'Maintenance mensuelle', 'description' => 'Maintenance et mise à jour du site', 'default_price' => 200.00],
            ['name' => 'Design logo', 'description' => 'Création de logo professionnel', 'default_price' => 350.00],
            ['name' => 'SEO Optimisation', 'description' => 'Optimisation pour moteurs de recherche', 'default_price' => 450.00],
            ['name' => 'Formation WordPress', 'description' => 'Formation utilisateur WordPress (1 jour)', 'default_price' => 600.00],
            ['name' => 'Hébergement annuel', 'description' => 'Hébergement web premium', 'default_price' => 180000.00],
            ['name' => 'Application mobile', 'description' => 'Développement application mobile', 'default_price' => 2500000.00],
            ['name' => 'Support technique', 'description' => 'Support technique mensuel', 'default_price' => 150000.00],
            ['name' => 'Pack Marketing Digital', 'description' => 'Stratégie marketing complète', 'default_price' => 750000.00],
            ['name' => 'Maintenance Serveur', 'description' => 'Maintenance serveur mensuelle', 'default_price' => 300000.00],
            ['name' => 'Formation Sécurité', 'description' => 'Formation sécurité informatique', 'default_price' => 400000.00],
            ['name' => 'Audit Système', 'description' => 'Audit complet du système', 'default_price' => 850000.00],
            ['name' => 'Configuration Réseau', 'description' => 'Installation et configuration réseau', 'default_price' => 550000.00],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'default_price' => $product['default_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
