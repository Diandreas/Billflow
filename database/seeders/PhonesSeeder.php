<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phone;
use App\Models\Client;

class PhonesSeeder extends Seeder
{
    public function run()
    {
        // Le PhonesSeeder n'est plus nécessaire car les téléphones 
        // sont déjà créés dans le ClientsSeeder
        
        // Mais on pourrait ajouter des téléphones supplémentaires ici si besoin
        // Exemple:
        /*
        $phones = [
            '+33 7 12 34 56 78', '+33 7 23 45 67 89', '+33 7 34 56 78 90',
            '+237 650 123 456', '+237 691 234 567', '+237 671 345 678',
        ];

        foreach ($phones as $phoneNumber) {
            // Vérifier si le numéro existe déjà
            if (!Phone::where('number', $phoneNumber)->exists()) {
                Phone::create([
                    'number' => $phoneNumber,
                ]);
            }
        }
        */
    }
}
