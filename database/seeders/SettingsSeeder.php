<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Configuration initiale de l'application
     */
    public function run(): void
    {
        $this->command->info('Configuration des paramètres de l\'application...');

        Setting::create([
            'company_name' => 'BILLFLOW SARL',
            'address' => 'Rue Principale, Quartier Commercial\nDouala, Cameroun',
            'phone' => '+237 655 123 456',
            'email' => 'contact@billflow.com',
            'siret' => 'RC/DLA/2023/B/1234',
            'tax_rate' => 19.25,
            'currency' => 'XAF',
            'logo_path' => null,
            'invoice_prefix' => 'FACT-',
            'invoice_footer' => 'Merci pour votre confiance!\nPaiement à effectuer sous 30 jours.\nCoordonnées bancaires: 10005 00001 12345678901 90',
            'default_payment_terms' => 30,
            'default_due_days' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Paramètres configurés avec succès.');
    }
}
