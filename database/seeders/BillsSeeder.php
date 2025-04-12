<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;
use Faker\Factory as Faker;

class BillsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');
        $clients = Client::all();
        $products = Product::all();
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::now();

        // Statuts possibles des factures
        $statuses = ['Payé', 'En attente', 'Annulé', 'En retard'];
        
        // Méthodes de paiement
        $paymentMethods = ['Virement bancaire', 'Espèces', 'Mobile Money', 'Carte bancaire', 'Chèque'];

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

            // Sélectionne aléatoirement les clients pour ce jour
            $dailyClients = $clients->random(min($numberOfBills, count($clients)));

            foreach ($dailyClients as $client) {
                // Ajoute une heure aléatoire à la date (entre 8h et 18h)
                $billDateTime = $date->copy()->addHours($faker->numberBetween(8, 18))->addMinutes($faker->numberBetween(0, 59));
                
                // Détermine le statut de la facture (plus de chances d'être payée pour les factures anciennes)
                $daysSinceCreation = $billDateTime->diffInDays(Carbon::now());
                $isPaid = $daysSinceCreation > 30 ? $faker->boolean(90) : $faker->boolean(60);
                
                // Choix du statut en fonction de diverses conditions
                if ($isPaid) {
                    $status = 'Payé';
                    $paidDate = $billDateTime->copy()->addDays($faker->numberBetween(0, 15));
                    $paymentMethod = $faker->randomElement($paymentMethods);
                } else {
                    // Si non payée, déterminer si elle est en attente, annulée ou en retard
                    if ($daysSinceCreation > 30) {
                        $status = $faker->randomElement(['En retard', 'Annulé']);
                    } else {
                        $status = 'En attente';
                    }
                    $paidDate = null;
                    $paymentMethod = null;
                }
                
                // Génère un numéro de facture plus réaliste
                $billNumber = $date->format('Ymd') . '-' . sprintf('%04d', $client->id);
                
                // Commentaire éventuel sur la facture
                $comment = null;
                if ($faker->boolean(20)) {
                    $commentOptions = [
                        'Livraison urgente requise',
                        'Client fidèle - appliquer remise',
                        'Premier achat',
                        'Demande spéciale pour la configuration',
                        'Service après-vente inclus pendant 1 an',
                        'Client référé par partenaire',
                        'Formation à prévoir après installation'
                    ];
                    $comment = $faker->randomElement($commentOptions);
                }
                
                // Déterminer le nombre de produits pour cette facture (1 à 4)
                $productsForBill = $products->random($faker->numberBetween(1, 4));
                
                // Calculer le total de la facture
                $totalAmount = 0;
                $totalTax = 0;
                $billItems = [];
                
                foreach ($productsForBill as $product) {
                    $quantity = $faker->numberBetween(1, 3);
                    // Prix unitaire avec légère variation possible (réductions ou augmentations)
                    $unitPrice = $product->default_price * $faker->randomFloat(2, 0.9, 1.1);
                    $itemTotal = $unitPrice * $quantity;
                    
                    // TVA standard de 19.25%
                    $itemTax = $itemTotal * 0.1925;
                    
                    $totalAmount += $itemTotal;
                    $totalTax += $itemTax;
                    
                    $billItems[] = [
                        'product_id' => $product->id,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total' => $itemTotal,
                        'created_at' => $billDateTime,
                        'updated_at' => $billDateTime,
                    ];
                }
                
                // Arrondir pour éviter les problèmes de précision
                $totalAmount = round($totalAmount, 2);
                $totalTax = round($totalTax, 2);
                
                // Date d'échéance (15 jours après émission)
                $dueDate = $billDateTime->copy()->addDays(15);

                // Créer la facture
                $bill = Bill::create([
                    'reference' => 'FACT-' . $billNumber,
                    'description' => 'Facture pour services rendus à ' . $client->name,
                    'total' => $totalAmount,
                    'date' => $billDateTime,
                    'due_date' => $dueDate,
                    'tax_rate' => 19.25,
                    'tax_amount' => $totalTax,
                    'user_id' => 1,
                    'client_id' => $client->id,
                    'created_at' => $billDateTime,
                    'updated_at' => $billDateTime,
                    'status' => $status,
                    'payment_date' => $paidDate,
                    'payment_method' => $paymentMethod,
                    'comments' => $comment,
                ]);

                // Associer les produits à la facture
                foreach ($billItems as $item) {
                    $bill->products()->attach($item['product_id'], [
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],
                    ]);
                }
            }
        }
        
        // Créer quelques factures récurrentes mensuelles pour des services d'abonnement
        $subscriptionClients = $clients->random(5);
        $subscriptionProducts = Product::where('name', 'like', '%maintenance%')
            ->orWhere('name', 'like', '%mensuel%')
            ->orWhere('name', 'like', '%abonnement%')
            ->get();
            
        if ($subscriptionProducts->count() == 0) {
            $subscriptionProducts = $products->random(3);
        }
        
        foreach ($subscriptionClients as $client) {
            $product = $subscriptionProducts->random();
            $startMonth = Carbon::create(2023, $faker->numberBetween(1, 6), 1);
            
            // Créer des factures mensuelles
            for ($month = $startMonth->copy(); $month->lte($endDate); $month->addMonth()) {
                $billDateTime = $month->copy()->setDay($faker->numberBetween(1, 5));
                
                // Les factures d'abonnement sont généralement payées
                $isPaid = $faker->boolean(90);
                
                if ($isPaid) {
                    $status = 'Payé';
                    $paidDate = $billDateTime->copy()->addDays($faker->numberBetween(0, 10));
                    $paymentMethod = $faker->randomElement($paymentMethods);
                } else {
                    $status = $faker->randomElement(['En attente', 'En retard']);
                    $paidDate = null;
                    $paymentMethod = null;
                }
                
                $billNumber = 'ABO-' . $month->format('Ym') . '-' . sprintf('%04d', $client->id);
                $unitPrice = $product->default_price;
                $totalAmount = $unitPrice;
                $totalTax = $totalAmount * 0.1925;

                // Date d'échéance (fin du mois)
                $dueDate = $month->copy()->endOfMonth();

                // Créer la facture d'abonnement
                $bill = Bill::create([
                    'reference' => 'FACT-' . $billNumber,
                    'description' => 'Abonnement mensuel - ' . $product->name . ' - ' . $month->format('F Y'),
                    'total' => $totalAmount,
                    'date' => $billDateTime,
                    'due_date' => $dueDate,
                    'tax_rate' => 19.25,
                    'tax_amount' => $totalTax,
                    'user_id' => 1,
                    'client_id' => $client->id,
                    'created_at' => $billDateTime,
                    'updated_at' => $billDateTime,
                    'status' => $status,
                    'payment_date' => $paidDate,
                    'payment_method' => $paymentMethod,
                    'comments' => 'Facturation récurrente mensuelle',
                    'is_recurring' => true,
                ]);

                // Associer le produit d'abonnement à la facture
                $bill->products()->attach($product->id, [
                    'unit_price' => $unitPrice,
                    'quantity' => 1,
                    'total' => $totalAmount,
                    'created_at' => $billDateTime,
                    'updated_at' => $billDateTime,
                ]);
            }
        }
    }
}
