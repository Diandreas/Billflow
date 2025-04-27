<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class BasicShopSeeder extends Seeder
{
    /**
     * Crée une boutique par défaut pour la production
     */
    public function run(): void
    {
        $this->command->info('Création de la boutique par défaut...');

        // Création de la boutique principale
        $shop = Shop::create([
            'name' => 'BillFlow Siège',
            'address' => '123 Avenue Principale',
            'city' => 'Douala',
            'region' => 'Littoral',
            'phone' => '+237 655123456',
            'email' => 'boutique@billflow.com',
            'description' => 'Boutique principale du système BillFlow',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Boutique créée: ' . $shop->name);
    }
}
