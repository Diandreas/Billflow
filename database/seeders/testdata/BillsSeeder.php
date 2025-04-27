<?php

namespace Database\Seeders\testdata;

use App\Models\Bill;
use App\Models\Client;
use App\Models\Commission;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BillsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->error('Aucun client trouvé. Veuillez exécuter ClientsSeeder d\'abord.');
            return;
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->error('Aucun produit trouvé. Veuillez exécuter ProductsSeeder d\'abord.');
            return;
        }

        $shops = Shop::all();
        if ($shops->isEmpty()) {
            $this->command->error('Aucune boutique trouvée. Veuillez exécuter UserAndShopSeeder d\'abord.');
            return;
        }

        $sellers = User::where('role', 'vendeur')->get();
        if ($sellers->isEmpty()) {
            $this->command->error('Aucun vendeur trouvé. Veuillez exécuter UserAndShopSeeder d\'abord.');
            return;
        }

        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::now();

        // Statuts possibles des factures
        $statuses = ['paid', 'pending', 'cancelled', 'overdue'];

        // Méthodes de paiement
        $paymentMethods = ['Virement bancaire', 'Espèces', 'Mobile Money', 'Carte bancaire', 'Chèque'];

        $this->command->info('Création de factures pour la période du ' . $startDate->format('d/m/Y') . ' au ' . $endDate->format('d/m/Y'));

        // Compteur de factures créées
        $billCount = 0;
        $commissionCount = 0;
        $inventoryMovementCount = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Génère un nombre aléatoire de factures pour ce jour
            // Le weekend (samedi et dimanche), moins de chances d'avoir des factures
            $isWeekend = $date->isWeekend();

            // Probabilité de 30% de n'avoir aucune facture en semaine, 70% le weekend
            if (($isWeekend && $faker->boolean(70)) || (!$isWeekend && $faker->boolean(30))) {
                continue; // Passe au jour suivant sans créer de factures
            }

            // En semaine : 1 à 8 factures, weekend : 0 à 3 factures
            $numberOfBills = $isWeekend ? $faker->numberBetween(0, 3) : $faker->numberBetween(1, 8);

            // Sélectionne aléatoirement les boutiques pour ce jour
            $dailyShops = $shops->random(min($numberOfBills, count($shops)));

            foreach ($dailyShops as $shop) {
                // Sélectionne aléatoirement un client
                $client = $clients->random();

                // Sélectionne les vendeurs de cette boutique ou n'importe quel vendeur si aucun
                $shopSellers = $shop->users()->where('role', 'vendeur')->get();
                if ($shopSellers->isEmpty()) {
                    $shopSellers = $sellers;
                }
                $seller = $shopSellers->random();

                // Ajoute une heure aléatoire à la date (entre 8h et 18h)
                $billDateTime = $date->copy()->addHours($faker->numberBetween(8, 18))->addMinutes($faker->numberBetween(0, 59));

                // Détermine le statut de la facture
                $daysSinceCreation = $billDateTime->diffInDays(Carbon::now());
                $paymentProbability = min(90, 60 + $daysSinceCreation / 2); // Plus la facture est ancienne, plus elle a de chances d'être payée
                $isPaid = $faker->boolean($paymentProbability);

                // Choix du statut et date de paiement
                if ($isPaid) {
                    $status = 'paid';
                    $paidDate = $billDateTime->copy()->addDays($faker->numberBetween(0, 15));
                    $paymentMethod = $faker->randomElement($paymentMethods);
                } else {
                    if ($daysSinceCreation > 30 && $faker->boolean(70)) {
                        $status = $faker->randomElement(['overdue', 'cancelled']);
                    } else {
                        $status = 'pending';
                    }
                    $paidDate = null;
                    $paymentMethod = null;
                }

                // Génère une référence de facture
                $reference = 'FACT-' . $date->format('Ymd') . '-' . str_pad(++$billCount, 4, '0', STR_PAD_LEFT);

                // Sélectionne 1 à 5 produits pour cette facture
                $productsForBill = $products->random($faker->numberBetween(1, 5));

                // Calculer le total de la facture
                $subtotal = 0;
                $billItems = [];

                foreach ($productsForBill as $product) {
                    $quantity = $faker->numberBetween(1, 5);

                    // Utiliser default_price au lieu de price (qui n'existe pas)
                    $basePrice = $product->default_price;

                    // Plus grande variation de prix: ±20% de variation possible selon le vendeur
                    $variationFactor = $faker->randomFloat(2, 0.8, 1.2);

                    // Appliquer une petite augmentation pour certains vendeurs avec commission élevée
                    if ($seller->commission_rate > 4) {
                        $variationFactor *= 1.05; // 5% de plus pour les vendeurs à commission élevée
                    }

                    $unitPrice = $basePrice * $variationFactor;

                    // Assurer un prix minimum et arrondir à un nombre entier
                    $unitPrice = max(round($unitPrice), 1000);

                    $itemTotal = $unitPrice * $quantity;

                    $subtotal += $itemTotal;

                    $billItems[] = [
                        'product_id' => $product->id,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total' => $itemTotal,
                    ];
                }

                // Calcul des taxes
                $taxRate = 19.25; // Taux standard
                $taxAmount = $subtotal * ($taxRate / 100);
                $totalWithTax = $subtotal + $taxAmount;

                // Date d'échéance (15 jours après émission)
                $dueDate = $billDateTime->copy()->addDays(15);

                // Créer la facture
                $bill = Bill::create([
                    'reference' => $reference,
                    'description' => 'Facture pour ' . $client->name,
                    'total' => $totalWithTax,
                    'date' => $billDateTime,
                    'due_date' => $dueDate,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'user_id' => $seller->id,
                    'client_id' => $client->id,
                    'shop_id' => $shop->id,
                    'status' => $status,
                    'created_at' => $billDateTime,
                    'updated_at' => $billDateTime,
                ]);

                // Associer les produits à la facture
                foreach ($billItems as $item) {
                    $bill->products()->attach($item['product_id'], [
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'created_at' => $billDateTime,
                        'updated_at' => $billDateTime,
                    ]);

                    // Créer les mouvements d'inventaire correspondants
                    $product = Product::find($item['product_id']);

                    // Calculer les niveaux de stock avant/après
                    $stockBefore = $product->stock_quantity + $item['quantity'];
                    $stockAfter = $product->stock_quantity;

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'vente',
                        'quantity' => -$item['quantity'], // Négatif car c'est une sortie
                        'reference' => $reference,
                        'bill_id' => $bill->id,
                        'user_id' => $seller->id,
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'created_at' => $billDateTime,
                        'updated_at' => $billDateTime,
                    ]);

                    $inventoryMovementCount++;
                }

                // Créer la commission du vendeur si la facture est payée
                if ($status === 'paid' && $seller->commission_rate > 0) {
                    $commissionRate = $seller->commission_rate;
                    $commissionAmount = $totalWithTax * ($commissionRate / 100);

                    Commission::create([
                        'user_id' => $seller->id,
                        'bill_id' => $bill->id,
                        'shop_id' => $shop->id,
                        'amount' => $commissionAmount,
                        'rate' => $commissionRate,
                        'base_amount' => $totalWithTax,
                        'type' => 'vente',
                        'is_paid' => $faker->boolean(60), // 60% de chances que la commission soit payée
                        'created_at' => $paidDate,
                        'updated_at' => $paidDate,
                    ]);

                    $commissionCount++;
                }

                $billCount++;

                // Affiche un message tous les 50 factures
                if ($billCount % 50 === 0) {
                    $this->command->info("$billCount factures créées...");
                }
            }
        }

        $this->command->info("Création terminée avec succès !");
        $this->command->info("$billCount factures créées");
        $this->command->info("$commissionCount commissions créées");
        $this->command->info("$inventoryMovementCount mouvements d'inventaire créés");
    }
}
