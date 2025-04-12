<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Phone;
use Faker\Factory as Faker;

class ClientsSeeder extends Seeder
{
    public function run()
    {
        // Utiliser Faker pour générer des données aléatoires
        $faker = Faker::create('fr_FR');
        
        // Données fixes pour assurer une diversité prévisible
        $clients = [
            ['name' => 'Société ABC', 'email' => 'contact@abc.com', 'sex' => 'M', 'birth' => '1985-03-15', 'phone' => '+237 691234567', 'address' => '123 Avenue Centrale, Douala', 'notes' => 'Client fondateur'],
            ['name' => 'Marie Dubois', 'email' => 'marie.dubois@email.com', 'sex' => 'F', 'birth' => '1990-07-22', 'phone' => '+237 677889900', 'address' => '45 Rue des Fleurs, Yaoundé', 'notes' => 'Partenaire fidèle'],
            ['name' => 'Tech Solutions SARL', 'email' => 'info@techsolutions.com', 'sex' => 'M', 'birth' => '1978-11-30', 'phone' => '+237 698765432', 'address' => '78 Boulevard Tech, Douala', 'notes' => 'Grand compte'],
            ['name' => 'Restaurant Le Gourmet', 'email' => 'reservation@legourmet.com', 'sex' => 'F', 'birth' => '1982-05-18', 'phone' => '+237 655443322', 'address' => '15 Avenue Gastronomie, Kribi', 'notes' => 'Référé par Marie Dubois'],
            ['name' => 'Cabinet Medical Dr. Martin', 'email' => 'drmartin@cabinet.com', 'sex' => 'M', 'birth' => '1975-09-25', 'phone' => '+237 699887766', 'address' => '92 Rue de la Santé, Yaoundé', 'notes' => null],
            ['name' => 'SARL Tech Solutions', 'email' => 'contact@techsolutions.fr', 'sex' => 'M', 'birth' => '1985-06-15', 'phone' => '+237 677123456', 'address' => '25 Avenue Innovation, Douala', 'notes' => 'Client prioritaire'],
            ['name' => 'Marie Dubois Consulting', 'email' => 'info@mdconsulting.com', 'sex' => 'F', 'birth' => '1990-03-22', 'phone' => '+237 698001122', 'address' => '10 Rue Conseil, Bafoussam', 'notes' => null],
            ['name' => 'Entreprise KAMDEM', 'email' => 'contact@kamdem.com', 'sex' => 'M', 'birth' => '1982-11-30', 'phone' => '+237 677889955', 'address' => '56 Boulevard Commerce, Limbe', 'notes' => 'Contrat annuel'],
            ['name' => 'Cabinet Dr. FOTSO', 'email' => 'drfotso@cabinet.com', 'sex' => 'F', 'birth' => '1978-04-18', 'phone' => '+237 699112233', 'address' => '33 Rue Médecine, Yaoundé', 'notes' => null],
            ['name' => 'Restaurant Le Bantou', 'email' => 'info@lebantou.com', 'sex' => 'M', 'birth' => '1988-09-25', 'phone' => '+237 655667788', 'address' => '77 Avenue Cuisine, Buea', 'notes' => 'Projet en cours'],
            ['name' => 'École STEM Academy', 'email' => 'admin@stemacademy.edu', 'sex' => 'F', 'birth' => '1992-07-14', 'phone' => '+237 677445566', 'address' => '101 Rue Education, Douala', 'notes' => 'Contrat pluriannuel'],
            ['name' => 'Boutique Mode Express', 'email' => 'boutique@modeexpress.com', 'sex' => 'F', 'birth' => '1989-12-03', 'phone' => '+237 698223344', 'address' => '28 Avenue Fashion, Yaoundé', 'notes' => null],
            ['name' => 'Garage Auto Plus', 'email' => 'service@autoplus.com', 'sex' => 'M', 'birth' => '1975-08-21', 'phone' => '+237 677334455', 'address' => '63 Rue Mécanique, Douala', 'notes' => null],
            ['name' => 'Pharmacie Centrale', 'email' => 'info@pharmaciecentrale.com', 'sex' => 'F', 'birth' => '1983-05-17', 'phone' => '+237 699556677', 'address' => '9 Boulevard Santé, Yaoundé', 'notes' => 'Bon payeur'],
            ['name' => 'Supermarché Ongola', 'email' => 'service@ongola.com', 'sex' => 'M', 'birth' => '1980-01-30', 'phone' => '+237 655778899', 'address' => '42 Avenue Commerce, Douala', 'notes' => 'Grand compte'],
            ['name' => 'Librairie Savoirs Plus', 'email' => 'contact@savoirsplus.com', 'sex' => 'M', 'birth' => '1979-08-23', 'phone' => '+237 677665544', 'address' => '17 Rue Culture, Yaoundé', 'notes' => null],
            ['name' => 'Boulangerie Le Pain Doré', 'email' => 'bonjour@paindore.com', 'sex' => 'F', 'birth' => '1990-02-28', 'phone' => '+237 698445566', 'address' => '31 Boulevard Boulangerie, Bamenda', 'notes' => null],
            ['name' => 'Cabinet Juridique Équité', 'email' => 'contact@equite.com', 'sex' => 'M', 'birth' => '1977-11-05', 'phone' => '+237 677112233', 'address' => '5 Avenue Justice, Yaoundé', 'notes' => 'Partenaire'],
            ['name' => 'Gym Club Vitalité', 'email' => 'info@vitalite.com', 'sex' => 'F', 'birth' => '1993-06-17', 'phone' => '+237 699334455', 'address' => '22 Rue Sport, Douala', 'notes' => null],
        ];

        // Créer les clients fixes
        foreach ($clients as $clientData) {
            // Créer le client
            $client = Client::create([
                'name' => $clientData['name'],
                'email' => $clientData['email'],
                'sex' => $clientData['sex'],
                'birth' => $clientData['birth'],
                'address' => $clientData['address'],
                'notes' => $clientData['notes'],
                'user_id' => 1, // Attribuer tous les clients à l'utilisateur ID 1
            ]);
            
            // Créer et associer le téléphone
            $phone = Phone::create([
                'number' => $clientData['phone']
            ]);
            
            $client->phones()->attach($phone->id);
        }
        
        // Générer des clients supplémentaires aléatoires (30 clients)
        for ($i = 0; $i < 30; $i++) {
            $sex = $faker->randomElement(['M', 'F']);
            $firstName = ($sex === 'M') ? $faker->firstNameMale : $faker->firstNameFemale;
            
            // Alternance entre particuliers et entreprises
            if ($i % 3 == 0) {
                // Entreprise
                $name = $faker->company;
                $email = $faker->companyEmail;
            } else {
                // Particulier
                $name = $firstName . ' ' . $faker->lastName;
                $email = $faker->email;
            }
            
            // Créer le client
            $client = Client::create([
                'name' => $name,
                'email' => $email,
                'sex' => $sex,
                'birth' => $faker->date('Y-m-d', '-18 years'),
                'address' => $faker->streetAddress . ', ' . $faker->randomElement(['Douala', 'Yaoundé', 'Bafoussam', 'Limbé', 'Kribi', 'Garoua', 'Ngaoundéré', 'Buea']),
                'notes' => $faker->boolean(30) ? $faker->sentence() : null,
                'user_id' => 1,
            ]);
            
            // Créer et associer le téléphone
            $phone = Phone::create([
                'number' => '+237 6' . $faker->numberBetween(55, 99) . $faker->numerify('######')
            ]);
            
            $client->phones()->attach($phone->id);
        }
    }
}
