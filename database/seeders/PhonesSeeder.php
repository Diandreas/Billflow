<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phone;
use App\Models\Client;

class PhonesSeeder extends Seeder
{
    public function run()
    {
        $phones = [
            '+33 6 12 34 56 78', '+33 6 23 45 67 89', '+33 6 34 56 78 90', '+33 6 45 67 89 01', '+33 6 56 78 90 12',
            '+237 655 123 456', '+237 699 234 567', '+237 677 345 678', '+237 651 456 789', '+237 694 567 890',
            '+237 676 678 901', '+237 698 789 012', '+237 652 890 123', '+237 697 901 234', '+237 675 012 345',
            '+237 655 234 567', '+237 699 345 678', '+237 677 456 789', '+237 651 567 890', '+237 694 678 901',
        ];

        $clients = Client::all();

        foreach ($clients as $index => $client) {
            // Utiliser un numéro de téléphone existant si l'index dépasse la taille du tableau $phones
            $phoneNumber = $phones[$index % count($phones)];

            $phone = Phone::create([
                'number' => $phoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $client->phones()->attach($phone->id);
        }
    }
}
