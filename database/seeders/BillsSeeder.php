<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;

class BillsSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $products = Product::all();
        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2024, 12, 31);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Génère un nombre aléatoire de factures pour ce jour
            // Le weekend (samedi et dimanche), moins de chances d'avoir des factures
            $isWeekend = $date->isWeekend();

            // Probabilité de 30% de n'avoir aucune facture en semaine, 70% le weekend
            if (($isWeekend && rand(1, 100) <= 70) || (!$isWeekend && rand(1, 100) <= 30)) {
                continue; // Passe au jour suivant sans créer de factures
            }

            // En semaine : 1 à 8 factures, weekend : 0 à 3 factures
            $numberOfBills = $isWeekend ? rand(0, 3) : rand(1, 8);

            // Sélectionne aléatoirement les clients pour ce jour
            $dailyClients = $clients->random(min($numberOfBills, count($clients)));

            foreach ($dailyClients as $client) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->default_price;
                $total = $unitPrice * $quantity;
                $taxAmount = $total * 0.1925;

                // Ajoute une heure aléatoire à la date (entre 8h et 18h)
                $billDateTime = $date->copy()->addHours(rand(8, 18))->addMinutes(rand(0, 59));

                $bill = Bill::create([
                    'reference' => 'FACT-' . $billDateTime->format('Y-m-d') . '-CLIENT' . $client->id,
                    'description' => 'Facture pour le ' . $billDateTime->format('d-m-Y'),
                    'total' => $total,
                    'date' => $billDateTime,
                    'tax_rate' => 19.25,
                    'tax_amount' => $taxAmount,
                    'user_id' => 1,
                    'client_id' => $client->id,
                    'created_at' => $billDateTime,
                    'updated_at' => $billDateTime,
                    'status' => 'Payé',
                ]);

                $bill->products()->attach($product->id, [
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total' => $total,
                    'created_at' => $billDateTime,
                    'updated_at' => $billDateTime,
                ]);
            }
        }
    }
}
