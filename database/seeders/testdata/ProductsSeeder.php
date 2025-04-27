<?php

namespace Database\Seeders\testdata;

use App\Models\Product;
use App\Models\ProductCategory;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');

        // Définir des catégories de produits
        $categories = [
            'Services Web' => 'Services liés au développement et maintenance web',
            'Services Mobile' => 'Applications et développement mobile',
            'Design & Graphisme' => 'Services de design et création graphique',
            'Marketing Digital' => 'Services de marketing et promotion en ligne',
            'Infrastructure IT' => 'Services d\'infrastructure et réseaux',
            'Formation' => 'Services de formation et coaching',
            'Conseil' => 'Services de conseil et consultation',
            'Produits Physiques' => 'Équipements et matériels informatiques',
        ];

        // Créer les catégories
        $categoryIds = [];
        foreach ($categories as $name => $description) {
            $category = ProductCategory::create([
                'name' => $name,
                'description' => $description,
                'slug' => \Illuminate\Support\Str::slug($name),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryIds[$name] = $category->id;
        }

        // Produits organisés par catégorie
        $products = [
            // Services Web
            [
                'name' => 'Développement site web vitrine',
                'description' => 'Création site web vitrine responsive avec design moderne',
                'default_price' => 750000.00,
                'category' => 'Services Web',
                'sku' => 'WEB-001',
                'type' => 'service',
                'stock_quantity' => 0, // Les services n'ont pas de stock
                'stock_alert_threshold' => null,
                'cost_price' => 250000.00
            ],
            [
                'name' => 'Développement site e-commerce',
                'description' => 'Création boutique en ligne complète avec gestion de paiements',
                'default_price' => 1500000.00,
                'category' => 'Services Web',
                'sku' => 'WEB-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Maintenance mensuelle site',
                'description' => 'Maintenance et mise à jour mensuelle du site web',
                'default_price' => 100000.00,
                'category' => 'Services Web',
                'sku' => 'WEB-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Hébergement annuel Premium',
                'description' => 'Hébergement web premium avec garantie de disponibilité 99.9%',
                'default_price' => 180000.00,
                'category' => 'Services Web',
                'sku' => 'WEB-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Refonte site web',
                'description' => 'Refonte complète de site web existant avec nouveau design',
                'default_price' => 1200000.00,
                'category' => 'Services Web',
                'sku' => 'WEB-005',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Services Mobile
            [
                'name' => 'Application mobile Android',
                'description' => 'Développement application mobile native Android',
                'default_price' => 2000000.00,
                'category' => 'Services Mobile',
                'sku' => 'MOB-001',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Application mobile iOS',
                'description' => 'Développement application mobile native iOS',
                'default_price' => 2200000.00,
                'category' => 'Services Mobile',
                'sku' => 'MOB-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Application mobile hybride',
                'description' => 'Développement application mobile hybride (Android + iOS)',
                'default_price' => 2500000.00,
                'category' => 'Services Mobile',
                'sku' => 'MOB-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Maintenance application mobile',
                'description' => 'Maintenance et mises à jour trimestrielles application mobile',
                'default_price' => 150000.00,
                'category' => 'Services Mobile',
                'sku' => 'MOB-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Design & Graphisme
            [
                'name' => 'Design logo professionnel',
                'description' => 'Création de logo professionnel avec pack complet de fichiers sources',
                'default_price' => 350000.00,
                'category' => 'Design & Graphisme',
                'sku' => 'DES-001',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Identité visuelle complète',
                'description' => 'Création d\'identité visuelle complète (logo, carte de visite, papier en-tête)',
                'default_price' => 650000.00,
                'category' => 'Design & Graphisme',
                'sku' => 'DES-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Design UI/UX site web',
                'description' => 'Conception interface utilisateur et expérience utilisateur site web',
                'default_price' => 800000.00,
                'category' => 'Design & Graphisme',
                'sku' => 'DES-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Création bannière publicitaire',
                'description' => 'Création de bannières publicitaires pour web et réseaux sociaux',
                'default_price' => 150000.00,
                'category' => 'Design & Graphisme',
                'sku' => 'DES-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Marketing Digital
            [
                'name' => 'SEO Optimisation avancée',
                'description' => 'Optimisation complète pour moteurs de recherche avec audit et suivi',
                'default_price' => 450000.00,
                'category' => 'Marketing Digital',
                'sku' => 'MKT-001',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Campagne Google Ads',
                'description' => 'Conception et gestion campagne Google Ads mensuelle',
                'default_price' => 350000.00,
                'category' => 'Marketing Digital',
                'sku' => 'MKT-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Gestion réseaux sociaux',
                'description' => 'Gestion mensuelle complète des réseaux sociaux',
                'default_price' => 300000.00,
                'category' => 'Marketing Digital',
                'sku' => 'MKT-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Pack Marketing Digital complet',
                'description' => 'Stratégie marketing complète (SEO, SEM, réseaux sociaux, email)',
                'default_price' => 750000.00,
                'category' => 'Marketing Digital',
                'sku' => 'MKT-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Infrastructure IT
            [
                'name' => 'Maintenance Serveur mensuelle',
                'description' => 'Maintenance serveur et infrastructure mensuelle',
                'default_price' => 300000.00,
                'category' => 'Infrastructure IT',
                'sku' => 'IT-001',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Audit Système complet',
                'description' => 'Audit complet du système IT avec recommandations',
                'default_price' => 850000.00,
                'category' => 'Infrastructure IT',
                'sku' => 'IT-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Configuration Réseau d\'entreprise',
                'description' => 'Installation et configuration réseau d\'entreprise complet',
                'default_price' => 550000.00,
                'category' => 'Infrastructure IT',
                'sku' => 'IT-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Solution de sauvegarde Cloud',
                'description' => 'Mise en place solution de sauvegarde automatisée dans le cloud',
                'default_price' => 450000.00,
                'category' => 'Infrastructure IT',
                'sku' => 'IT-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Formation
            [
                'name' => 'Formation WordPress (1 jour)',
                'description' => 'Formation utilisateur WordPress pour gestion de contenu',
                'default_price' => 200000.00,
                'category' => 'Formation',
                'sku' => 'FOR-001',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Formation Sécurité informatique',
                'description' => 'Formation sécurité informatique pour employés (2 jours)',
                'default_price' => 400000.00,
                'category' => 'Formation',
                'sku' => 'FOR-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Formation marketing digital',
                'description' => 'Formation aux bases du marketing digital (3 jours)',
                'default_price' => 450000.00,
                'category' => 'Formation',
                'sku' => 'FOR-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Formation suite Office',
                'description' => 'Formation à la suite Microsoft Office (Word, Excel, PowerPoint)',
                'default_price' => 250000.00,
                'category' => 'Formation',
                'sku' => 'FOR-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Conseil
            [
                'name' => 'Consultation stratégie digitale',
                'description' => 'Consultation sur la stratégie digitale d\'entreprise',
                'default_price' => 500000.00,
                'category' => 'Conseil',
                'sku' => 'CON-001',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Conseil en transformation digitale',
                'description' => 'Conseil pour la transformation digitale d\'entreprise',
                'default_price' => 950000.00,
                'category' => 'Conseil',
                'sku' => 'CON-002',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Consulting IT (journée)',
                'description' => 'Service de consultation IT à la journée',
                'default_price' => 300000.00,
                'category' => 'Conseil',
                'sku' => 'CON-003',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],
            [
                'name' => 'Audit et conseil RGPD',
                'description' => 'Audit et recommandations pour conformité RGPD',
                'default_price' => 650000.00,
                'category' => 'Conseil',
                'sku' => 'CON-004',
                'type' => 'service',
                'stock_quantity' => 0,
                'stock_alert_threshold' => null,
                'cost_price' => null
            ],

            // Produits Physiques (nouveaux produits avec gestion de stock)
            [
                'name' => 'Ordinateur portable ProBook',
                'description' => 'Ordinateur portable professionnel avec processeur i7, 16GB RAM, 512GB SSD',
                'default_price' => 850000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-001',
                'type' => 'physical',
                'stock_quantity' => 15,
                'stock_alert_threshold' => 3,
                'cost_price' => 650000.00
            ],
            [
                'name' => 'Écran 27" UltraHD',
                'description' => 'Écran 27 pouces avec résolution 4K et port HDMI/DisplayPort',
                'default_price' => 250000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-002',
                'type' => 'physical',
                'stock_quantity' => 8,
                'stock_alert_threshold' => 2,
                'cost_price' => 180000.00
            ],
            [
                'name' => 'Clavier mécanique RGB',
                'description' => 'Clavier mécanique avec rétroéclairage RGB et switches Cherry MX',
                'default_price' => 85000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-003',
                'type' => 'physical',
                'stock_quantity' => 25,
                'stock_alert_threshold' => 5,
                'cost_price' => 55000.00
            ],
            [
                'name' => 'Souris sans fil ergonomique',
                'description' => 'Souris sans fil avec capteur laser haute précision et batterie longue durée',
                'default_price' => 45000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-004',
                'type' => 'physical',
                'stock_quantity' => 30,
                'stock_alert_threshold' => 8,
                'cost_price' => 28000.00
            ],
            [
                'name' => 'Imprimante laser couleur',
                'description' => 'Imprimante laser couleur recto-verso avec connectivité réseau',
                'default_price' => 325000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-005',
                'type' => 'physical',
                'stock_quantity' => 5,
                'stock_alert_threshold' => 2,
                'cost_price' => 250000.00
            ],
            [
                'name' => 'Disque SSD 1TB',
                'description' => 'Disque SSD 1TB avec vitesse de lecture/écriture 550/520 MB/s',
                'default_price' => 120000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-006',
                'type' => 'physical',
                'stock_quantity' => 20,
                'stock_alert_threshold' => 5,
                'cost_price' => 85000.00
            ],
            [
                'name' => 'Serveur rack 2U',
                'description' => 'Serveur rack 2U avec double processeur Xeon et 64GB RAM',
                'default_price' => 2500000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-007',
                'type' => 'physical',
                'stock_quantity' => 3,
                'stock_alert_threshold' => 1,
                'cost_price' => 1800000.00
            ],
            [
                'name' => 'Switch réseau 48 ports',
                'description' => 'Switch réseau manageable 48 ports Gigabit avec 4 ports SFP+',
                'default_price' => 450000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-008',
                'type' => 'physical',
                'stock_quantity' => 7,
                'stock_alert_threshold' => 2,
                'cost_price' => 320000.00
            ],
            [
                'name' => 'Onduleur 3000VA',
                'description' => 'Onduleur 3000VA avec ports USB et écran LCD',
                'default_price' => 280000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-009',
                'type' => 'physical',
                'stock_quantity' => 6,
                'stock_alert_threshold' => 2,
                'cost_price' => 210000.00
            ],
            [
                'name' => 'Caméra de surveillance IP',
                'description' => 'Caméra IP HD avec vision nocturne et détection de mouvement',
                'default_price' => 75000.00,
                'category' => 'Produits Physiques',
                'sku' => 'PHY-010',
                'type' => 'physical',
                'stock_quantity' => 12,
                'stock_alert_threshold' => 3,
                'cost_price' => 45000.00
            ],
        ];

        // Créer les produits
        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'default_price' => $product['default_price'],
                'category_id' => $categoryIds[$product['category']],
                'sku' => $product['sku'],
                'type' => $product['type'],
                'stock_quantity' => $product['stock_quantity'] ?? 0,
                'stock_alert_threshold' => $product['stock_alert_threshold'] ?? null,
                'cost_price' => $product['cost_price'] ?? null,
                'status' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajouter quelques produits aléatoires supplémentaires
        for ($i = 0; $i < 10; $i++) {
            $categoryName = $faker->randomElement(array_keys($categories));
            $sku = strtoupper(substr($categoryName, 0, 3)) . '-' . sprintf('%03d', $i + 100);

            // Déterminer si c'est un produit physique ou un service
            $isPhysical = $categoryName === 'Produits Physiques' || $faker->boolean(20);
            $type = $isPhysical ? 'physical' : 'service';

            // Générer des valeurs de stock uniquement pour les produits physiques
            $stockQuantity = $isPhysical ? $faker->numberBetween(0, 50) : 0;
            $stockAlertThreshold = $isPhysical ? $faker->numberBetween(1, 5) : null;

            // Prix de vente et coût
            $defaultPrice = $faker->numberBetween(50000, 2000000);
            $costPrice = $isPhysical ? $defaultPrice * $faker->randomFloat(2, 0.5, 0.8) : null;

            Product::create([
                'name' => $faker->words(3, true),
                'description' => $faker->sentence(8),
                'default_price' => $defaultPrice,
                'category_id' => $categoryIds[$categoryName],
                'sku' => $sku,
                'type' => $type,
                'stock_quantity' => $stockQuantity,
                'stock_alert_threshold' => $stockAlertThreshold,
                'cost_price' => $costPrice,
                'status' => 'actif',
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
