<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\Feature;
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
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $featureIds[$feature['name']] = $feat->id;
        }
        
        // Définir les plans d'abonnement
        $plans = [
            [
                'name' => 'Gratuit',
                'description' => 'Plan de base pour les petites entreprises et les freelances débutants',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'max_users' => 1,
                'trial_days' => 0,
                'is_active' => true,
                'is_featured' => false,
                'details' => 'Accès limité aux fonctionnalités de base',
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
                'description' => 'Parfait pour les petites entreprises qui commencent à se développer',
            'price' => 15000,
            'billing_cycle' => 'monthly',
                'max_users' => 3,
                'trial_days' => 14,
                'is_active' => true,
                'is_featured' => true,
                'details' => 'Toutes les fonctionnalités de base avec quelques fonctionnalités avancées',
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
                'description' => 'Solution complète pour les PME et entreprises en pleine croissance',
                'price' => 45000,
                'billing_cycle' => 'monthly',
                'max_users' => 10,
                'trial_days' => 14,
                'is_active' => true,
                'is_featured' => true,
                'details' => 'Fonctionnalités avancées et support prioritaire',
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
                'description' => 'Solution complète et personnalisable pour grandes entreprises',
                'price' => 90000,
                'billing_cycle' => 'monthly',
                'max_users' => 30,
                'trial_days' => 30,
                'is_active' => true,
                'is_featured' => false,
                'details' => 'Toutes les fonctionnalités avec personnalisation et support dédié',
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
            [
                'name' => 'Sur Mesure',
                'description' => 'Solution entièrement personnalisée pour besoins spécifiques',
                'price' => null, // Prix à définir selon besoins
                'billing_cycle' => 'yearly',
                'max_users' => null, // Illimité ou à définir
                'trial_days' => 0,
                'is_active' => true,
                'is_featured' => false,
                'details' => 'Contactez notre équipe commerciale pour une solution adaptée à vos besoins spécifiques',
                'features' => [
                    ['name' => 'Personnalisation', 'value' => 'Complète et sur mesure'],
                    ['name' => 'Support technique', 'value' => 'Support VIP avec équipe dédiée'],
                    ['name' => 'Formation incluse', 'value' => 'Programme de formation complet'],
                ]
            ],
        ];
        
        // Créer également des plans annuels avec remise pour les plans payants
        $annualPlans = [];
        foreach ($plans as $plan) {
            if ($plan['price'] > 0) {
                $annualPlan = $plan;
                $annualPlan['name'] = $plan['name'] . ' (Annuel)';
                $annualPlan['description'] = 'Version annuelle du plan ' . $plan['name'] . ' avec 15% de réduction';
                $annualPlan['price'] = $plan['price'] * 12 * 0.85; // 15% de réduction sur le prix annuel
                $annualPlan['billing_cycle'] = 'yearly';
                $annualPlan['is_featured'] = false;
                
                $annualPlans[] = $annualPlan;
            }
        }
        
        // Fusionner les plans mensuels et annuels
        $allPlans = array_merge($plans, $annualPlans);
        
        // Créer les plans d'abonnement
        foreach ($allPlans as $planData) {
            $planFeatures = $planData['features'];
            unset($planData['features']);
            
            // Si le prix est null, mettre "Sur devis" dans la description
            if ($planData['price'] === null) {
                $planData['price_description'] = 'Sur devis';
            } else {
                $planData['price_description'] = number_format($planData['price'], 0, ',', ' ') . ' FCFA/' . 
                    ($planData['billing_cycle'] === 'monthly' ? 'mois' : 'an');
            }
            
            // Créer le plan
            $plan = SubscriptionPlan::create($planData);
            
            // Attacher les caractéristiques au plan
            foreach ($planFeatures as $feature) {
                if (isset($featureIds[$feature['name']])) {
                    $plan->features()->attach($featureIds[$feature['name']], [
                        'value' => $feature['value'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        // Créer quelques offres promotionnelles pour les plans
        $promotions = [
            [
                'name' => 'Offre de lancement',
                'description' => 'Profitez de 30% de réduction sur les 3 premiers mois',
                'discount_percentage' => 30,
                'valid_from' => Carbon\Carbon::now()->subMonth(),
                'valid_until' => Carbon\Carbon::now()->addMonths(2),
                'code' => 'LAUNCH30',
                'plans' => ['Essentiel', 'Professionnel']
            ],
            [
                'name' => 'Offre Black Friday',
                'description' => '50% de réduction sur le premier trimestre pour toute souscription annuelle',
                'discount_percentage' => 50,
                'valid_from' => Carbon\Carbon::now(),
                'valid_until' => Carbon\Carbon::now()->addWeeks(2),
                'code' => 'BLACKFRIDAY50',
                'plans' => ['Essentiel (Annuel)', 'Professionnel (Annuel)', 'Entreprise (Annuel)']
            ],
            [
                'name' => 'Offre Fidélité',
                'description' => '20% de réduction supplémentaire pour les clients existants',
                'discount_percentage' => 20,
                'valid_from' => Carbon\Carbon::now(),
                'valid_until' => Carbon\Carbon::now()->addMonths(6),
                'code' => 'FIDELITE20',
                'plans' => ['Professionnel', 'Entreprise']
            ]
        ];
        
        // Pour chaque promotion, créer l'entrée dans la base de données
        foreach ($promotions as $promo) {
            $planIds = [];
            foreach ($promo['plans'] as $planName) {
                $plan = SubscriptionPlan::where('name', $planName)->first();
                if ($plan) {
                    $planIds[] = $plan->id;
                }
            }
            
            // Créer la promotion
            $promotion = \App\Models\Promotion::create([
                'name' => $promo['name'],
                'description' => $promo['description'],
                'discount_percentage' => $promo['discount_percentage'],
                'valid_from' => $promo['valid_from'],
                'valid_until' => $promo['valid_until'],
                'code' => $promo['code'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Attacher les plans à cette promotion
            $promotion->plans()->attach($planIds);
        }
    }
} 