<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Setting::create([
            'company_name' => 'BILLFLOW SARL',
            'address' => 'Rue 1.110, Bastos\nYaoundé, Cameroun',
            'phone' => '+237 655 555 555',
            'email' => 'contact@billflow.cm',
            'siret' => 'RC/YAE/2024/B/',
            'tax_rate' => 19.25,
            'currency' => 'XAF',
            'invoice_prefix' => 'FACT-',
            'invoice_footer' => 'Merci de votre confiance!\nPaiement à 30 jours\nRIB: 10005 00001 12345678901 90',
            'default_payment_terms' => 30,
            'default_due_days' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
