<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\Feature;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');
        
        // Créer d'abord les caractéristiques (features)
        $features = [
            // Caractéristiques générales
            ['name' => 'Support technique', 'description' => 'Assistance technique par email et téléphone'],
            ['name' => 'Mises à jour gratuites', 'description' => 'Accès aux mises à jour de la plateforme'],
            ['name' => 'Sauvegarde automatique', 'description' => 'Sauvegarde quotidienne des données'],
            ['name' => 'API Access', 'description' => 'Accès à l\'API pour l\'intégration avec d\'autres systèmes'],
            ['name' => 'Formation incluse', 'description' => 'Formation de base pour les utilisateurs'],
            ['name' => 'Support premium', 'description' => 'Support prioritaire avec temps de réponse garanti'],
            ['name' => 'Personnalisation', 'description' => 'Options de personnalisation avancées'],
            
            // Caractéristiques spécifiques à la facturation
            ['name' => 'Nombre de factures mensuelles', 'description' => 'Nombre maximum de factures à générer par mois'],
            ['name' => 'Nombre de clients', 'description' => 'Nombre maximum de clients dans la base de données'],
            ['name' => 'Nombre de produits', 'description' => 'Nombre maximum de produits/services configurables'],
            ['name' => 'Factures récurrentes', 'description' => 'Possibilité de configurer des factures récurrentes'],
            ['name' => 'Relances automatiques', 'description' => 'Envoi automatique de relances pour factures impayées'],
            ['name' => 'Devis et conversion', 'description' => 'Création de devis et conversion en factures'],
            ['name' => 'Factures multilingues', 'description' => 'Génération de factures en plusieurs langues'],
            ['name' => 'Rapports avancés', 'description' => 'Accès aux rapports et analyses avancés'],
            ['name' => 'Paiement en ligne', 'description' => 'Intégration des paiements en ligne'],
            ['name' => 'Mode hors ligne', 'description' => 'Utilisation de l\'application sans connexion internet'],
            ['name' => 'Personnalisation des modèles', 'description' => 'Personnalisation complète des modèles de factures'],
            ['name' => 'Export comptable', 'description' => 'Export de données pour logiciels comptables']
        ];
        
        // Créer les caractéristiques dans la base de données
        $featureIds = [];
        foreach ($features as $feature) {
            $feat = Feature::create([
                'name' => $feature['name'],
                'description' => $feature['description'],
            ]);
            
            $featureIds[$feature['name']] = $feat->id;
        }
        
        // Définir les plans d'abonnement
        $plans = [
            [
                'name' => 'Gratuit',
                'code' => 'FREE',
                'description' => 'Plan de base pour les petites entreprises et les freelances débutants',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'max_clients' => 20,
                'campaigns_per_cycle' => 2,
                'sms_quota' => 50,
                'sms_personal_quota' => 10,
                'is_active' => true,
                'features' => [
                    ['name' => 'Nombre de factures mensuelles', 'value' => '10'],
                    ['name' => 'Nombre de clients', 'value' => '20'],
                    ['name' => 'Nombre de produits', 'value' => '10'],
                    ['name' => 'Sauvegarde automatique', 'value' => 'Hebdomadaire'],
                    ['name' => 'Support technique', 'value' => 'Email uniquement'],
                    ['name' => 'Mises à jour gratuites', 'value' => 'Inclus'],
                ]
            ],
            [
                'name' => 'Essentiel',
                'code' => 'ESSENTIAL',
                'description' => 'Parfait pour les petites entreprises qui commencent à se développer',
                'price' => 15000,
                'billing_cycle' => 'monthly',
                'max_clients' => 100,
                'campaigns_per_cycle' => 5,
                'sms_quota' => 200,
                'sms_personal_quota' => 50,
                'is_active' => true,
                'features' => [
                    ['name' => 'Nombre de factures mensuelles', 'value' => '100'],
                    ['name' => 'Nombre de clients', 'value' => '100'],
                    ['name' => 'Nombre de produits', 'value' => '50'],
                    ['name' => 'Sauvegarde automatique', 'value' => 'Quotidienne'],
                    ['name' => 'Support technique', 'value' => 'Email et Chat'],
                    ['name' => 'Mises à jour gratuites', 'value' => 'Inclus'],
                    ['name' => 'Factures récurrentes', 'value' => 'Inclus'],
                    ['name' => 'Devis et conversion', 'value' => 'Inclus'],
                    ['name' => 'Export comptable', 'value' => 'Formats de base'],
                ]
            ],
            [
                'name' => 'Professionnel',
                'code' => 'PRO',
                'description' => 'Solution complète pour les PME et entreprises en pleine croissance',
                'price' => 45000,
                'billing_cycle' => 'monthly',
                'max_clients' => 500,
                'campaigns_per_cycle' => 10,
                'sms_quota' => 1000,
                'sms_personal_quota' => 200,
                'is_active' => true,
                'features' => [
                    ['name' => 'Nombre de factures mensuelles', 'value' => 'Illimité'],
                    ['name' => 'Nombre de clients', 'value' => '500'],
                    ['name' => 'Nombre de produits', 'value' => '200'],
                    ['name' => 'Sauvegarde automatique', 'value' => 'Quotidienne'],
                    ['name' => 'Support technique', 'value' => 'Email, Chat et Téléphone'],
                    ['name' => 'Support premium', 'value' => 'Inclus'],
                    ['name' => 'Mises à jour gratuites', 'value' => 'Inclus'],
                    ['name' => 'Factures récurrentes', 'value' => 'Inclus'],
                    ['name' => 'Relances automatiques', 'value' => 'Inclus'],
                    ['name' => 'Devis et conversion', 'value' => 'Inclus'],
                    ['name' => 'Factures multilingues', 'value' => 'Inclus'],
                    ['name' => 'Personnalisation des modèles', 'value' => 'Basique'],
                    ['name' => 'Export comptable', 'value' => 'Tous formats'],
                    ['name' => 'Rapports avancés', 'value' => 'Inclus'],
                    ['name' => 'API Access', 'value' => 'Accès limité'],
                ]
            ],
            [
                'name' => 'Entreprise',
                'code' => 'ENTERPRISE',
                'description' => 'Solution complète et personnalisable pour grandes entreprises',
                'price' => 90000,
                'billing_cycle' => 'monthly',
                'max_clients' => 1000,
                'campaigns_per_cycle' => 30,
                'sms_quota' => 5000,
                'sms_personal_quota' => 500,
                'is_active' => true,
                'features' => [
                    ['name' => 'Nombre de factures mensuelles', 'value' => 'Illimité'],
                    ['name' => 'Nombre de clients', 'value' => 'Illimité'],
                    ['name' => 'Nombre de produits', 'value' => 'Illimité'],
                    ['name' => 'Sauvegarde automatique', 'value' => 'Temps réel'],
                    ['name' => 'Support technique', 'value' => 'Support prioritaire 24/7'],
                    ['name' => 'Support premium', 'value' => 'Inclus avec gestionnaire dédié'],
                    ['name' => 'Mises à jour gratuites', 'value' => 'Inclus avec installation prioritaire'],
                    ['name' => 'Formation incluse', 'value' => '3 sessions de formation incluses'],
                    ['name' => 'Factures récurrentes', 'value' => 'Inclus avec planification avancée'],
                    ['name' => 'Relances automatiques', 'value' => 'Inclus avec workflows personnalisables'],
                    ['name' => 'Devis et conversion', 'value' => 'Inclus avec suivi avancé'],
                    ['name' => 'Factures multilingues', 'value' => 'Inclus (toutes langues)'],
                    ['name' => 'Personnalisation', 'value' => 'Complète'],
                    ['name' => 'Personnalisation des modèles', 'value' => 'Avancée avec designer dédié'],
                    ['name' => 'Export comptable', 'value' => 'Tous formats avec intégration directe'],
                    ['name' => 'Rapports avancés', 'value' => 'Inclus avec tableaux de bord personnalisables'],
                    ['name' => 'API Access', 'value' => 'Accès complet'],
                    ['name' => 'Paiement en ligne', 'value' => 'Intégration multi-passerelles'],
                    ['name' => 'Mode hors ligne', 'value' => 'Inclus'],
                ]
            ],
        ];
        
        // Créer également des plans annuels avec remise pour les plans payants
        $annualPlans = [];
        foreach ($plans as $plan) {
            if ($plan['price'] > 0) {
                $annualPlan = $plan;
                $annualPlan['name'] = $plan['name'] . ' (Annuel)';
                $annualPlan['code'] = $plan['code'] . '_ANNUAL';
                $annualPlan['description'] = 'Version annuelle du plan ' . $plan['name'] . ' avec 15% de réduction';
                $annualPlan['price'] = $plan['price'] * 12 * 0.85; // 15% de réduction sur le prix annuel
                $annualPlan['billing_cycle'] = 'yearly';
                
                $annualPlans[] = $annualPlan;
            }
        }
        
        // Fusionner les plans mensuels et annuels
        $allPlans = array_merge($plans, $annualPlans);
        
        // Créer les plans d'abonnement
        foreach ($allPlans as $planData) {
            $planFeatures = $planData['features'];
            unset($planData['features']);
            
            // Créer le plan
            $plan = SubscriptionPlan::create($planData);
            
            // Attacher les caractéristiques au plan
            foreach ($planFeatures as $feature) {
                if (isset($featureIds[$feature['name']])) {
                    $plan->features()->attach($featureIds[$feature['name']], [
                        'value' => $feature['value']
                    ]);
                }
            }
        }
    }
} 