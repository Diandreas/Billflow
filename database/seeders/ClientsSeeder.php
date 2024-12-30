<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientsSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            ['name' => 'Société ABC', 'sex' => 'M', 'birth' => '1985-03-15'],
            ['name' => 'Marie Dubois', 'sex' => 'F', 'birth' => '1990-07-22'],
            ['name' => 'Tech Solutions SARL', 'sex' => 'M', 'birth' => '1978-11-30'],
            ['name' => 'Restaurant Le Gourmet', 'sex' => 'F', 'birth' => '1982-05-18'],
            ['name' => 'Cabinet Medical Dr. Martin', 'sex' => 'M', 'birth' => '1975-09-25'],
            ['name' => 'SARL Tech Solutions', 'sex' => 'M', 'birth' => '1985-06-15'],
            ['name' => 'Marie Dubois Consulting', 'sex' => 'F', 'birth' => '1990-03-22'],
            ['name' => 'Entreprise KAMDEM', 'sex' => 'M', 'birth' => '1982-11-30'],
            ['name' => 'Cabinet Dr. FOTSO', 'sex' => 'F', 'birth' => '1978-04-18'],
            ['name' => 'Restaurant Le Bantou', 'sex' => 'M', 'birth' => '1988-09-25'],
            ['name' => 'École STEM Academy', 'sex' => 'F', 'birth' => '1992-07-14'],
            ['name' => 'Boutique Mode Express', 'sex' => 'F', 'birth' => '1989-12-03'],
            ['name' => 'Garage Auto Plus', 'sex' => 'M', 'birth' => '1975-08-21'],
            ['name' => 'Pharmacie Centrale', 'sex' => 'F', 'birth' => '1983-05-17'],
            ['name' => 'Supermarché Ongola', 'sex' => 'M', 'birth' => '1980-01-30'],
            ['name' => 'Librairie Savoirs Plus', 'sex' => 'M', 'birth' => '1979-08-23'],
            ['name' => 'Boulangerie Le Pain Doré', 'sex' => 'F', 'birth' => '1990-02-28'],
            ['name' => 'Cabinet Juridique Équité', 'sex' => 'M', 'birth' => '1977-11-05'],
            ['name' => 'Gym Club Vitalité', 'sex' => 'F', 'birth' => '1993-06-17'],
        ];

        foreach ($clients as $client) {
            Client::create([
                'name' => $client['name'],
                'sex' => $client['sex'],
                'birth' => $client['birth'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
