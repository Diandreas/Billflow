<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryMovementSeeder extends Seeder
{
    public function run()
    {
        // Vider la table des mouvements d'inventaire existants
        DB::table('inventory_movements')->truncate();
        
        $faker = Faker::create();
        $products = Product::where('type', 'physical')->get();
        $users = User::all();
        $bills = Bill::all();
        
        if ($products->isEmpty()) {
            $this->command->info('Aucun produit physique trouvé. Aucun mouvement d\'inventaire créé.');
            return;
        }
        
        $totalMovements = 0;
        
        // Réinitialiser tous les stocks des produits physiques à zéro
        foreach ($products as $product) {
            $product->update(['stock_quantity' => 0]);
        }
        
        // Création des mouvements d'inventaire pour chaque produit physique
        foreach ($products as $product) {
            // 1. Mouvement d'entrée initial (stock initial)
            $initialDate = Carbon::now()->subMonths($faker->numberBetween(10, 12));
            $initialQuantity = $faker->numberBetween(20, 50);
            
            // Création du mouvement d'entrée initial
            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'entrée',
                'quantity' => $initialQuantity,
                'reference' => 'Stock initial',
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'notes' => 'Approvisionnement initial',
                'unit_price' => $product->cost_price ?? ($product->default_price * 0.6),
                'total_price' => ($product->cost_price ?? ($product->default_price * 0.6)) * $initialQuantity,
                'stock_before' => 0,
                'stock_after' => $initialQuantity,
                'created_at' => $initialDate
            ]);
            
            $totalMovements++;
            $currentStock = $initialQuantity;
            
            // 2. Mouvements périodiques (sur 10 mois)
            for ($month = 9; $month >= 0; $month--) {
                $monthStart = Carbon::now()->subMonths($month)->startOfMonth();
                $monthEnd = Carbon::now()->subMonths($month)->endOfMonth();
                
                // 2.1 Entrées de stock (réapprovisionnements)
                $entriesCount = $faker->numberBetween(1, 3);
                
                for ($i = 0; $i < $entriesCount; $i++) {
                    $entryDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                    $entryQuantity = $faker->numberBetween(5, 20);
                    
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'entrée',
                        'quantity' => $entryQuantity,
                        'reference' => $faker->randomElement(['Commande fournisseur #', 'Réapprovisionnement #', 'Livraison #']) . $faker->randomNumber(5),
                        'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                        'notes' => $faker->randomElement(['Réapprovisionnement régulier', 'Commande urgente', 'Réassort', null]),
                        'unit_price' => $product->cost_price ?? ($product->default_price * 0.6),
                        'total_price' => ($product->cost_price ?? ($product->default_price * 0.6)) * $entryQuantity,
                        'stock_before' => $currentStock,
                        'stock_after' => $currentStock + $entryQuantity,
                        'created_at' => $entryDate
                    ]);
                    
                    $currentStock += $entryQuantity;
                    $totalMovements++;
                }
                
                // 2.2 Sorties liées à des factures
                if ($bills->isNotEmpty() && $currentStock > 0) {
                    $billsForMonth = $bills->filter(function($bill) use ($monthStart, $monthEnd) {
                        $billDate = Carbon::parse($bill->date);
                        return $billDate->between($monthStart, $monthEnd);
                    });
                    
                    // Si nous avons des factures pour ce mois
                    if ($billsForMonth->isNotEmpty()) {
                        $billsToUse = $faker->numberBetween(1, min(3, $billsForMonth->count()));
                        $usedBills = $billsForMonth->random($billsToUse);
                        
                        foreach ($usedBills as $bill) {
                            // Limiter la quantité de sortie au stock disponible
                            $maxExitQuantity = min(3, $currentStock);
                            if ($maxExitQuantity <= 0) break;
                            
                            $exitQuantity = $faker->numberBetween(1, $maxExitQuantity);
                            
                            InventoryMovement::create([
                                'product_id' => $product->id,
                                'type' => 'sortie',
                                'quantity' => $exitQuantity,
                                'reference' => 'Facture #' . $bill->id,
                                'bill_id' => $bill->id,
                                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                                'notes' => 'Vente sur facture',
                                'unit_price' => $product->default_price,
                                'total_price' => $product->default_price * $exitQuantity,
                                'stock_before' => $currentStock,
                                'stock_after' => $currentStock - $exitQuantity,
                                'created_at' => $bill->date
                            ]);
                            
                            $currentStock -= $exitQuantity;
                            $totalMovements++;
                        }
                    }
                }
                
                // 2.3 Sorties non liées à des factures (ventes directes, usage interne, etc.)
                if ($currentStock > 0) {
                    $otherExitsCount = $faker->numberBetween(1, min(4, $currentStock));
                    
                    for ($i = 0; $i < $otherExitsCount; $i++) {
                        if ($currentStock <= 0) break;
                        
                        $exitDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                        $maxExitQuantity = min(2, $currentStock);
                        $exitQuantity = $faker->numberBetween(1, $maxExitQuantity);
                        
                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'sortie',
                            'quantity' => $exitQuantity,
                            'reference' => $faker->randomElement(['Vente directe #', 'Usage interne #', 'Prélèvement #']) . $faker->randomNumber(4),
                            'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                            'notes' => $faker->randomElement(['Vente au comptoir', 'Usage interne', 'Démonstration client', 'Échantillon']),
                            'unit_price' => $product->default_price,
                            'total_price' => $product->default_price * $exitQuantity,
                            'stock_before' => $currentStock,
                            'stock_after' => $currentStock - $exitQuantity,
                            'created_at' => $exitDate
                        ]);
                        
                        $currentStock -= $exitQuantity;
                        $totalMovements++;
                    }
                }
                
                // 2.4 Ajustements occasionnels (inventaire physique, corrections, etc.)
                if ($faker->boolean(20)) {
                    $adjustmentDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                    // Ajustements possibles: petite variation positive ou négative
                    $adjustment = $faker->randomElement([-2, -1, 1, 2]);
                    
                    // Éviter les stocks négatifs
                    if ($currentStock + $adjustment < 0) {
                        $adjustment = 0;
                    }
                    
                    if ($adjustment != 0) {
                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'ajustement',
                            'quantity' => $adjustment,
                            'reference' => 'Ajustement manuel',
                            'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                            'notes' => $faker->randomElement(['Inventaire physique', 'Correction d\'erreur', 'Produit endommagé', 'Écart constaté']),
                            'stock_before' => $currentStock,
                            'stock_after' => $currentStock + $adjustment,
                            'created_at' => $adjustmentDate
                        ]);
                        
                        $currentStock += $adjustment;
                        $totalMovements++;
                    }
                }
            }
            
            // Mettre à jour le stock final du produit
            $product->update(['stock_quantity' => $currentStock]);
        }
        
        $this->command->info($totalMovements . ' mouvements d\'inventaire ont été créés avec cohérence.');
    }
} 