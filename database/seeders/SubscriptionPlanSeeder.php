<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // Pack Starter
        SubscriptionPlan::create([
            'name' => 'Pack Starter',
            'code' => 'starter',
            'description' => 'Capacité: Jusqu\'à 100 clients dans la base de données
Messages promotionnels: 2 campagnes par mois (200 SMS au total)
Messages personnalisés: 50 SMS de réserve mensuelle
Renouvellement: Les SMS non utilisés expirent à la fin du mois',
            'price' => 5000,
            'billing_cycle' => 'monthly',
            'max_clients' => 100,
            'campaigns_per_cycle' => 2,
            'sms_quota' => 200,
            'sms_personal_quota' => 50,
            'sms_rollover_percent' => 0,
            'is_active' => true
        ]);

        // Pack Business
        SubscriptionPlan::create([
            'name' => 'Pack Business',
            'code' => 'business',
            'description' => 'Capacité: Jusqu\'à 500 clients
Messages promotionnels: 4 campagnes par mois (1.000 SMS au total)
Messages personnalisés: 200 SMS de réserve mensuelle
Renouvellement: 10% des SMS non utilisés sont reportés au mois suivant',
            'price' => 15000,
            'billing_cycle' => 'monthly',
            'max_clients' => 500,
            'campaigns_per_cycle' => 4,
            'sms_quota' => 1000,
            'sms_personal_quota' => 200,
            'sms_rollover_percent' => 10,
            'is_active' => true
        ]);

        // Pack Enterprise
        SubscriptionPlan::create([
            'name' => 'Pack Enterprise',
            'code' => 'enterprise',
            'description' => 'Capacité: Jusqu\'à 2.000 clients
Messages promotionnels: 8 campagnes par mois (4.000 SMS au total)
Messages personnalisés: 500 SMS de réserve mensuelle
Renouvellement: 20% des SMS non utilisés sont reportés au mois suivant',
            'price' => 30000,
            'billing_cycle' => 'monthly',
            'max_clients' => 2000,
            'campaigns_per_cycle' => 8,
            'sms_quota' => 4000,
            'sms_personal_quota' => 500,
            'sms_rollover_percent' => 20,
            'is_active' => true
        ]);

        // Version annuelle des packs
        SubscriptionPlan::create([
            'name' => 'Pack Starter (Annuel)',
            'code' => 'starter-yearly',
            'description' => 'Économisez avec l\'engagement annuel
Capacité: Jusqu\'à 100 clients dans la base de données
Messages promotionnels: 24 campagnes par an (2.400 SMS au total)
Messages personnalisés: 600 SMS de réserve annuelle
Renouvellement: Les SMS non utilisés expirent à la fin de l\'année',
            'price' => 50000,
            'billing_cycle' => 'yearly',
            'max_clients' => 100,
            'campaigns_per_cycle' => 24, // 2 * 12
            'sms_quota' => 2400, // 200 * 12
            'sms_personal_quota' => 600, // 50 * 12
            'sms_rollover_percent' => 0,
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Pack Business (Annuel)',
            'code' => 'business-yearly',
            'description' => 'Économisez avec l\'engagement annuel
Capacité: Jusqu\'à 500 clients
Messages promotionnels: 48 campagnes par an (12.000 SMS au total)
Messages personnalisés: 2.400 SMS de réserve annuelle
Renouvellement: 10% des SMS non utilisés sont reportés à l\'année suivante',
            'price' => 150000,
            'billing_cycle' => 'yearly',
            'max_clients' => 500,
            'campaigns_per_cycle' => 48, // 4 * 12
            'sms_quota' => 12000, // 1000 * 12
            'sms_personal_quota' => 2400, // 200 * 12
            'sms_rollover_percent' => 10,
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Pack Enterprise (Annuel)',
            'code' => 'enterprise-yearly',
            'description' => 'Économisez avec l\'engagement annuel
Capacité: Jusqu\'à 2.000 clients
Messages promotionnels: 96 campagnes par an (48.000 SMS au total)
Messages personnalisés: 6.000 SMS de réserve annuelle
Renouvellement: 20% des SMS non utilisés sont reportés à l\'année suivante',
            'price' => 300000,
            'billing_cycle' => 'yearly',
            'max_clients' => 2000,
            'campaigns_per_cycle' => 96, // 8 * 12
            'sms_quota' => 48000, // 4000 * 12
            'sms_personal_quota' => 6000, // 500 * 12
            'sms_rollover_percent' => 20,
            'is_active' => true
        ]);
    }
} 