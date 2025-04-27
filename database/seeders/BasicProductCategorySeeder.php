<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BasicProductCategorySeeder extends Seeder
{
    /**
     * Crée les catégories de produits de base et quelques produits d'exemple
     */
    public function run(): void
    {
        $this->command->info('Création des catégories de produits de base...');

        // Catégories principales
        $categories = [
            'Services' => 'Services professionnels et prestations',
            'Produits' => 'Produits physiques à vendre',
            'Formations' => 'Formations et ateliers',
        ];

        $categoryIds = [];

        // Création des catégories
        foreach ($categories as $name => $description) {
            $category = ProductCategory::create([
                'name' => $name,
                'description' => $description,
                'slug' => Str::slug($name),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $categoryIds[$name] = $category->id;
            $this->command->info('Catégorie créée: ' . $name);
        }

        // Création de quelques produits d'exemple
        $this->command->info('Création de produits d\'exemple...');

        $products = [
            // Services
            [
                'name' => 'Consultation technique',
                'description' => 'Consultation technique avec un expert (1 heure)',
                'default_price' => 25000,
                'category' => 'Services',
                'type' => 'service',
            ],
            // Produits physiques
            [
                'name' => 'Ordinateur portable standard',
                'description' => 'Ordinateur portable avec processeur i5, 8GB RAM, 256GB SSD',
                'default_price' => 450000,
                'category' => 'Produits',
                'type' => 'physical',
                'stock_quantity' => 10,
                'stock_alert_threshold' => 2,
                'cost_price' => 350000,
            ],
            // Formations
            [
                'name' => 'Formation d\'initiation',
                'description' => 'Formation d\'initiation aux outils numériques (1 jour)',
                'default_price' => 75000,
                'category' => 'Formations',
                'type' => 'service',
            ],
        ];

        // Création des produits
        foreach ($products as $productData) {
            Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'default_price' => $productData['default_price'],
                'category_id' => $categoryIds[$productData['category']],
                'type' => $productData['type'],
                'stock_quantity' => $productData['stock_quantity'] ?? 0,
                'stock_alert_threshold' => $productData['stock_alert_threshold'] ?? null,
                'cost_price' => $productData['cost_price'] ?? null,
                'status' => 'actif',
                'is_barterable' => $productData['type'] === 'physical',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Produits créés avec succès.');
    }
}
