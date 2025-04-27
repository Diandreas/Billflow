<?php

namespace Database\Seeders\testdata;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryMovementSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Création des mouvements d\'inventaire...');

        // Vider la table des mouvements d'inventaire existants
        DB::table('inventory_movements')->truncate();

        $faker = Faker::create('fr_FR');
        $products = Product::where('type', 'physical')->get();
        $users = User::whereIn('role', ['admin', 'manager', 'vendeur'])->get();
        $shops = Shop::all();

        if ($products->isEmpty()) {
            $this->command->error('Aucun produit physique trouvé. Aucun mouvement d\'inventaire créé.');
            return;
        }

        if ($shops->isEmpty()) {
            $this->command->error('Aucune boutique trouvée. Aucun mouvement d\'inventaire créé.');
            return;
        }

        $totalMovements = 0;

        // Réinitialiser tous les stocks des produits physiques à zéro
        foreach ($products as $product) {
            $product->update(['stock_quantity' => 0]);
        }

        // Création des mouvements d'inventaire pour chaque produit physique dans chaque boutique
        foreach ($shops as $shop) {
            $this->command->info('Création des mouvements pour la boutique : ' . $shop->name);

            // Récupérer les utilisateurs de cette boutique
            $shopUsers = $shop->users->filter(function($user) {
                return in_array($user->role, ['admin', 'manager', 'vendeur']);
            });

            if ($shopUsers->isEmpty()) {
                $shopUsers = $users;
            }

            // Distribuons les produits aux boutiques (tous les produits ne sont pas disponibles dans toutes les boutiques)
            $shopProducts = $products->random(min(count($products), $faker->numberBetween(10, count($products))));

            foreach ($shopProducts as $product) {
                // 1. Mouvement d'entrée initial (stock initial)
                $initialDate = Carbon::now()->subMonths($faker->numberBetween(10, 15));
                $initialQuantity = $faker->numberBetween(20, 100);
                $user = $shopUsers->random();

                // Création du mouvement d'entrée initial
                // Limiter le prix et la quantité pour éviter les débordements de total_price
                $initialQuantity = min($initialQuantity, 50); // Limiter la quantité à 50 max
                $unitPrice = $product->cost_price ?? ($product->price * 0.6);
                // S'assurer que le prix total ne dépasse pas la limite de decimal(10,2)
                if ($unitPrice * $initialQuantity > 9999999) {
                    $unitPrice = min($unitPrice, 9999999 / $initialQuantity);
                }

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'entrée',
                    'quantity' => $initialQuantity,
                    'reference' => 'Stock initial',
                    'user_id' => $user->id,
                    'notes' => 'Approvisionnement initial pour ' . $shop->name,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $initialQuantity,
                    'stock_before' => 0,
                    'stock_after' => $initialQuantity,
                    'created_at' => $initialDate,
                    'updated_at' => $initialDate
                ]);

                $totalMovements++;
                $currentStock = $initialQuantity;

                // 2. Mouvements périodiques (sur 10 mois)
                for ($month = 9; $month >= 0; $month--) {
                    $monthStart = Carbon::now()->subMonths($month)->startOfMonth();
                    $monthEnd = Carbon::now()->subMonths($month)->endOfMonth();

                    // 2.1 Entrées de stock (réapprovisionnements)
                    // Les produits populaires sont réapprovisionnés plus souvent
                    $entriesCount = $faker->numberBetween(1, 3);

                    for ($i = 0; $i < $entriesCount; $i++) {
                        // Si le stock est déjà élevé, moins de chances de réapprovisionner
                        if ($currentStock > 50 && $faker->boolean(70)) {
                            continue;
                        }

                        $entryDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                        $entryQuantity = $faker->numberBetween(5, 30);
                        $user = $shopUsers->random();

                        // Limiter la quantité et le prix unitaire pour éviter les débordements
                        $entryQuantity = min($entryQuantity, 20);
                        $unitPrice = $product->cost_price ?? ($product->price * 0.6);
                        if ($unitPrice * $entryQuantity > 9999999) {
                            $unitPrice = min($unitPrice, 9999999 / $entryQuantity);
                        }

                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'entrée',
                            'quantity' => $entryQuantity,
                            'reference' => $faker->randomElement(['Commande fournisseur #', 'Réapprovisionnement #', 'Livraison #']) . $faker->randomNumber(5),
                            'user_id' => $user->id,
                            'notes' => $faker->randomElement(['Réapprovisionnement régulier', 'Commande urgente', 'Réassort', null]) . ' pour ' . $shop->name,
                            'unit_price' => $unitPrice,
                            'total_price' => $unitPrice * $entryQuantity,
                            'stock_before' => $currentStock,
                            'stock_after' => $currentStock + $entryQuantity,
                            'created_at' => $entryDate,
                            'updated_at' => $entryDate
                        ]);

                        $currentStock += $entryQuantity;
                        $totalMovements++;
                    }

                    // 2.2 Sorties pour simulations de ventes
                    // Plus de ventes en période récente, et plus pour les produits populaires
                    $salesCount = $faker->numberBetween(0, 5);

                    for ($i = 0; $i < $salesCount; $i++) {
                        if ($currentStock <= 2) {
                            break; // Éviter les stocks négatifs
                        }

                        $exitDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                        $maxExitQuantity = min(5, $currentStock - 1);
                        $exitQuantity = $faker->numberBetween(1, $maxExitQuantity);
                        $user = $shopUsers->random();

                        // Limiter les valeurs pour éviter les débordements
                        $unitPrice = $product->price;
                        if ($unitPrice * $exitQuantity > 9999999) {
                            $unitPrice = min($unitPrice, 9999999 / $exitQuantity);
                        }

                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'vente',
                            'quantity' => -$exitQuantity, // Négatif car c'est une sortie
                            'reference' => 'Vente #' . $faker->randomNumber(6),
                            'user_id' => $user->id,
                            'notes' => $faker->randomElement(['Vente comptoir', 'Vente fidélité', 'Vente client régulier']) . ' à ' . $shop->name,
                            'unit_price' => $unitPrice,
                            'total_price' => $unitPrice * $exitQuantity,
                            'stock_before' => $currentStock,
                            'stock_after' => $currentStock - $exitQuantity,
                            'created_at' => $exitDate,
                            'updated_at' => $exitDate
                        ]);

                        $currentStock -= $exitQuantity;
                        $totalMovements++;
                    }

                    // 2.3 Ajustements occasionnels (inventaire physique, corrections, etc.)
                    if ($faker->boolean(15)) { // 15% de chances
                        $adjustmentDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                        // Ajustements possibles: petite variation positive ou négative
                        $adjustment = $faker->randomElement([-3, -2, -1, 1, 2, 3]);

                        // Éviter les stocks négatifs
                        if ($currentStock + $adjustment < 0) {
                            $adjustment = max(1 - $currentStock, -$currentStock + 1);
                        }

                        if ($adjustment != 0) {
                            $user = $shopUsers->where('role', 'manager')->random(); // Les managers font les ajustements

                            // Limiter les valeurs pour éviter les débordements
                            $unitPrice = $product->price;
                            if ($unitPrice * abs($adjustment) > 9999999) {
                                $unitPrice = min($unitPrice, 9999999 / abs($adjustment));
                            }

                            InventoryMovement::create([
                                'product_id' => $product->id,
                                'type' => 'ajustement',
                                'quantity' => $adjustment,
                                'reference' => 'Ajustement #' . $faker->randomNumber(4),
                                'user_id' => $user->id,
                                'notes' => $faker->randomElement(['Inventaire physique', 'Correction d\'erreur', 'Produit endommagé', 'Écart constaté']) . ' à ' . $shop->name,
                                'unit_price' => $unitPrice,
                                'total_price' => $unitPrice * abs($adjustment),
                                'stock_before' => $currentStock,
                                'stock_after' => $currentStock + $adjustment,
                                'created_at' => $adjustmentDate,
                                'updated_at' => $adjustmentDate
                            ]);

                            $currentStock += $adjustment;
                            $totalMovements++;
                        }
                    }

                    // 2.4 Transferts entre boutiques (si ce n'est pas la seule boutique)
                    if ($shops->count() > 1 && $faker->boolean(10) && $currentStock > 5) {
                        $transferDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                        $targetShop = $shops->where('id', '!=', $shop->id)->random();
                        $transferQuantity = $faker->numberBetween(1, min(5, $currentStock - 2));
                        $user = $shopUsers->random();

                        // Sortie de la boutique source
                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'transfert_sortie',
                            'quantity' => -$transferQuantity,
                            'reference' => 'Transfert vers ' . $targetShop->name,
                            'user_id' => $user->id,
                            'notes' => 'Transfert de stock depuis ' . $shop->name . ' vers ' . $targetShop->name,
                            'stock_before' => $currentStock,
                            'stock_after' => $currentStock - $transferQuantity,
                            'created_at' => $transferDate,
                            'updated_at' => $transferDate
                        ]);

                        $currentStock -= $transferQuantity;
                        $totalMovements++;

                        // Entrée dans la boutique destination
                        $transferArrivalDate = Carbon::parse($transferDate)->addHours(2); // Arrivée 2 heures plus tard

                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'transfert_entrée',
                            'quantity' => $transferQuantity,
                            'reference' => 'Transfert depuis ' . $shop->name,
                            'user_id' => $user->id,
                            'notes' => 'Transfert de stock depuis ' . $shop->name . ' vers ' . $targetShop->name,
                            'stock_before' => 0, // Valeur fictive car nous ne suivons pas les stocks par boutique ici
                            'stock_after' => $transferQuantity, // Valeur fictive
                            'created_at' => $transferArrivalDate,
                            'updated_at' => $transferArrivalDate
                        ]);

                        $totalMovements++;
                    }
                }

                // Incrémenter le stock global du produit (cumulatif pour toutes les boutiques)
                $existingStock = $product->stock_quantity ?? 0;
                $product->update(['stock_quantity' => $existingStock + $currentStock]);
            }
        }

        $this->command->info($totalMovements . ' mouvements d\'inventaire ont été créés avec cohérence entre boutiques.');
    }
}
