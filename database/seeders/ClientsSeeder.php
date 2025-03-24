<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientsSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            ['name' => 'Société ABC', 'email' => 'contact@abc.com', 'sex' => 'M', 'birth' => '1985-03-15'],
            ['name' => 'Marie Dubois', 'email' => 'marie.dubois@email.com', 'sex' => 'F', 'birth' => '1990-07-22'],
            ['name' => 'Tech Solutions SARL', 'email' => 'info@techsolutions.com', 'sex' => 'M', 'birth' => '1978-11-30'],
            ['name' => 'Restaurant Le Gourmet', 'email' => 'reservation@legourmet.com', 'sex' => 'F', 'birth' => '1982-05-18'],
            ['name' => 'Cabinet Medical Dr. Martin', 'email' => 'drmartin@cabinet.com', 'sex' => 'M', 'birth' => '1975-09-25'],
            ['name' => 'SARL Tech Solutions', 'email' => 'contact@techsolutions.fr', 'sex' => 'M', 'birth' => '1985-06-15'],
            ['name' => 'Marie Dubois Consulting', 'email' => 'info@mdconsulting.com', 'sex' => 'F', 'birth' => '1990-03-22'],
            ['name' => 'Entreprise KAMDEM', 'email' => 'contact@kamdem.com', 'sex' => 'M', 'birth' => '1982-11-30'],
            ['name' => 'Cabinet Dr. FOTSO', 'email' => 'drfotso@cabinet.com', 'sex' => 'F', 'birth' => '1978-04-18'],
            ['name' => 'Restaurant Le Bantou', 'email' => 'info@lebantou.com', 'sex' => 'M', 'birth' => '1988-09-25'],
            ['name' => 'École STEM Academy', 'email' => 'admin@stemacademy.edu', 'sex' => 'F', 'birth' => '1992-07-14'],
            ['name' => 'Boutique Mode Express', 'email' => 'boutique@modeexpress.com', 'sex' => 'F', 'birth' => '1989-12-03'],
            ['name' => 'Garage Auto Plus', 'email' => 'service@autoplus.com', 'sex' => 'M', 'birth' => '1975-08-21'],
            ['name' => 'Pharmacie Centrale', 'email' => 'info@pharmaciecentrale.com', 'sex' => 'F', 'birth' => '1983-05-17'],
            ['name' => 'Supermarché Ongola', 'email' => 'service@ongola.com', 'sex' => 'M', 'birth' => '1980-01-30'],
            ['name' => 'Librairie Savoirs Plus', 'email' => 'contact@savoirsplus.com', 'sex' => 'M', 'birth' => '1979-08-23'],
            ['name' => 'Boulangerie Le Pain Doré', 'email' => 'bonjour@paindore.com', 'sex' => 'F', 'birth' => '1990-02-28'],
            ['name' => 'Cabinet Juridique Équité', 'email' => 'contact@equite.com', 'sex' => 'M', 'birth' => '1977-11-05'],
            ['name' => 'Gym Club Vitalité', 'email' => 'info@vitalite.com', 'sex' => 'F', 'birth' => '1993-06-17'],
        ];

        foreach ($clients as $client) {
            Client::create([
                'name' => $client['name'],
                'email' => $client['email'],
                'sex' => $client['sex'],
                'birth' => $client['birth'],
                'user_id' => 1, // Attribuer tous les clients à l'utilisateur ID 1 (administrateur)
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
