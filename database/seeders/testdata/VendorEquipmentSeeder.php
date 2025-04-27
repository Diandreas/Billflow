<?php

namespace Database\Seeders\testdata;

use App\Models\Shop;
use App\Models\User;
use App\Models\VendorEquipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class VendorEquipmentSeeder extends Seeder
{
    public function run()
    {
        // Récupérer tous les vendeurs
        $vendors = User::where('role', 'vendeur')->get();

        if ($vendors->isEmpty()) {
            $this->command->info('Aucun vendeur trouvé. Exécutez d\'abord UserAndShopSeeder.');
            return;
        }

        // Types d'équipements possibles
        $equipmentTypes = [
            'Tablette',
            'Terminal de paiement',
            'Scanner code-barres',
            'Imprimante reçus',
            'Téléphone',
            'Badge',
            'Caisse enregistreuse'
        ];

        // Marques par type d'équipement
        $brandsByType = [
            'Tablette' => ['Apple', 'Samsung', 'Lenovo'],
            'Terminal de paiement' => ['Ingenico', 'Verifone', 'Adyen'],
            'Scanner code-barres' => ['Zebra', 'Honeywell', 'Datalogic'],
            'Imprimante reçus' => ['Epson', 'Star', 'Zebra'],
            'Téléphone' => ['Apple', 'Samsung', 'Google'],
            'Badge' => ['Generic'],
            'Caisse enregistreuse' => ['Casio', 'Sharp', 'IBM']
        ];

        // États possibles
        $states = ['returned', 'returned', 'assigned', 'assigned'];
        $shops_id = [1, 2, 3, 4];


        // Attribuer des équipements aux vendeurs
        foreach ($vendors as $vendor) {
            // Chaque vendeur reçoit entre 2 et 4 équipements
            $equipmentCount = rand(2, 4);

            // Tableau pour suivre les types déjà attribués
            $assignedTypes = [];

            for ($i = 0; $i < $equipmentCount; $i++) {
                // Sélectionner un type d'équipement qui n'a pas encore été attribué
                $availableTypes = array_diff($equipmentTypes, $assignedTypes);
                if (empty($availableTypes)) break;

                $type = Arr::random($availableTypes);
                $assignedTypes[] = $type;

                // Sélectionner une marque pour ce type
                $brand = Arr::random($brandsByType[$type]);

                // Générer un numéro de série aléatoire
                $serialNumber = strtoupper(substr($brand, 0, 3)) . '-' . rand(10000, 99999);

                // Déterminer si l'équipement est retourné (10% de chance)
                $isReturned = (rand(1, 10) === 1);
                $shops = Shop::all();

                // Créer l'équipement
                VendorEquipment::create([
                    'user_id' => $vendor->id,
                    'type' => $type,
                    'name'=> $type,
                    'brand' => $brand,
                    'model' => $brand . ' ' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2) . rand(100, 999),
                    'serial_number' => $serialNumber,
                    'status' => Arr::random($states),
                    'condition' => Arr::random($states),

                    'assigned_date' => now()->subDays(rand(1, 180)),
                    'assigned_at' => now()->subDays(rand(1, 180)),
                    'assigned_by' => $vendor->id,
                    'returned_at' => $isReturned ? now()->subDays(rand(1, 30)) : null,
                    'notes' => $isReturned ? 'Équipement retourné après utilisation' : 'Équipement actif',
                    'shop_id' =>Arr::random($shops_id),
                ]);
            }
        }
    }
}
