<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Liste des abonnements de l'utilisateur
     */
    public function index(Request $request)
    {
        $query = Subscription::where('user_id', Auth::id())
            ->with('plan');
            
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('plan', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Tri par date de début par défaut
        $query->latest('starts_at');
        
        $subscriptions = $query->paginate(10)->withQueryString();
        $activeSubscription = Auth::user()->activeSubscription;
        
        return view('subscriptions.index', compact('subscriptions', 'activeSubscription'));
    }

    /**
     * Afficher les plans d'abonnement disponibles
     */
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->groupBy('billing_cycle');
            
        $activeSubscription = Auth::user()->activeSubscription;
        
        return view('subscriptions.plans', compact('plans', 'activeSubscription'));
    }

    /**
     * Afficher le formulaire de souscription pour un plan spécifique
     */
    public function create(SubscriptionPlan $plan)
    {
        if (!$plan->is_active) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Ce plan n\'est plus disponible.');
        }
        
        return view('subscriptions.create', compact('plan'));
    }

    /**
     * Enregistrer un nouvel abonnement
     */
    public function store(Request $request, SubscriptionPlan $plan)
    {
        if (!$plan->is_active) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Ce plan n\'est plus disponible.');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:mobile_money,credit_card,bank_transfer',
            'terms_accepted' => 'required|accepted',
        ]);
        
        // Pour la démo, on créé l'abonnement directement sans paiement réel
        $startDate = Carbon::now();
        $endDate = $plan->billing_cycle === 'monthly' 
            ? $startDate->copy()->addMonth() 
            : $startDate->copy()->addYear();
        
        $subscription = new Subscription();
        $subscription->user_id = Auth::id();
        $subscription->subscription_plan_id = $plan->id;
        $subscription->starts_at = $startDate;
        $subscription->ends_at = $endDate;
        $subscription->price_paid = $plan->price;
        $subscription->status = 'active';
        $subscription->sms_remaining = $plan->sms_quota;
        $subscription->sms_personal_remaining = $plan->sms_personal_quota;
        $subscription->campaigns_used = 0;
        $subscription->transaction_reference = 'DEMO-' . time();
        $subscription->payment_data = [
            'method' => $validated['payment_method'],
            'date' => now()->format('Y-m-d H:i:s'),
            'status' => 'completed'
        ];
        $subscription->save();
        
        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Abonnement souscrit avec succès.');
    }

    /**
     * Afficher les détails d'un abonnement
     */
    public function show(Subscription $subscription)
    {
        // Vérifier que l'utilisateur est propriétaire de l'abonnement
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }
        
        $subscription->load('plan');
        
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Annuler un abonnement
     */
    public function cancel(Subscription $subscription)
    {
        // Vérifier que l'utilisateur est propriétaire de l'abonnement
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }
        
        $subscription->status = 'cancelled';
        $subscription->save();
        
        return redirect()->route('subscriptions.index')
            ->with('success', 'Abonnement annulé avec succès.');
    }

    /**
     * Afficher le formulaire pour recharger les SMS
     */
    public function rechargeForm()
    {
        $activeSubscription = Auth::user()->activeSubscription;
        
        if (!$activeSubscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour acheter des SMS supplémentaires.');
        }
        
        return view('subscriptions.recharge', compact('activeSubscription'));
    }

    /**
     * Procéder à la recharge de SMS
     */
    public function recharge(Request $request)
    {
        $validated = $request->validate([
            'sms_amount' => 'required|integer|min:100|max:5000',
            'payment_method' => 'required|in:mobile_money,credit_card,bank_transfer',
        ]);
        
        $activeSubscription = Auth::user()->activeSubscription;
        
        if (!$activeSubscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour acheter des SMS supplémentaires.');
        }
        
        // Calculer le coût (1000 FCFA pour 100 SMS)
        $smsAmount = $validated['sms_amount'];
        $cost = ($smsAmount / 100) * 1000;
        
        // Pour la démo, on ajoute directement les SMS sans paiement réel
        $activeSubscription->sms_remaining += $smsAmount;
        $activeSubscription->save();
        
        return redirect()->route('subscriptions.show', $activeSubscription)
            ->with('success', $smsAmount . ' SMS ajoutés à votre compte pour ' . number_format($cost, 0, ',', ' ') . ' FCFA');
    }
}
