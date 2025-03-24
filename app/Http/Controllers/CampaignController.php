<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\PromotionalMessage;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /**
     * Liste des campagnes de l'utilisateur connecté
     */
    public function index()
    {
        $campaigns = Campaign::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
            
        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Affiche le formulaire de création de campagne
     */
    public function create()
    {
        // Vérifier si l'utilisateur a un abonnement actif
        $subscription = Auth::user()->activeSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour créer des campagnes.');
        }
        
        // Vérifier si l'utilisateur a atteint sa limite de campagnes
        $campaignsUsed = $subscription->campaigns_used;
        $campaignsLimit = $subscription->plan->campaigns_per_cycle;
        
        if ($campaignsUsed >= $campaignsLimit) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Vous avez atteint votre limite de campagnes pour cette période.');
        }
        
        // Récupérer les clients de l'utilisateur pour le ciblage
        $clients = Client::where('user_id', Auth::id())->get();
        
        return view('campaigns.create', compact('clients', 'subscription'));
    }

    /**
     * Enregistrer une nouvelle campagne
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:birthday,holiday,promotion,custom',
            'message' => 'required|string|max:160',
            'target_segments' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);
        
        $subscription = Auth::user()->activeSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour créer des campagnes.');
        }
        
        // Créer la campagne
        $campaign = new Campaign();
        $campaign->user_id = Auth::id();
        $campaign->name = $validated['name'];
        $campaign->type = $validated['type'];
        $campaign->message = $validated['message'];
        $campaign->status = $request->has('scheduled_at') ? 'scheduled' : 'draft';
        $campaign->scheduled_at = $request->filled('scheduled_at') ? $validated['scheduled_at'] : null;
        $campaign->target_segments = $request->filled('target_segments') ? $validated['target_segments'] : null;
        $campaign->save();
        
        // Incrémenter le compteur de campagnes utilisées
        $subscription->campaigns_used += 1;
        $subscription->save();
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campagne créée avec succès.');
    }

    /**
     * Afficher les détails d'une campagne
     */
    public function show(Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Récupérer les statistiques de messages envoyés
        $messagesStats = [
            'messages_count' => PromotionalMessage::where('campaign_id', $campaign->id)->count(),
            'delivered_count' => PromotionalMessage::where('campaign_id', $campaign->id)
                ->where('status', 'delivered')->count(),
            'failed_count' => PromotionalMessage::where('campaign_id', $campaign->id)
                ->where('status', 'failed')->count(),
            'pending_count' => PromotionalMessage::where('campaign_id', $campaign->id)
                ->where('status', 'pending')->count(),
        ];
        
        // Ajouter les statistiques à l'objet campaign
        $campaign->messages_count = $messagesStats['messages_count'];
        $campaign->delivered_count = $messagesStats['delivered_count'];
        $campaign->failed_count = $messagesStats['failed_count'];
        $campaign->pending_count = $messagesStats['pending_count'];
        
        // Vérifier si l'utilisateur a un abonnement actif
        $subscription = Auth::user()->activeSubscription;
        
        return view('campaigns.show', compact('campaign', 'subscription'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Ne pas autoriser la modification des campagnes envoyées
        if ($campaign->status === 'sent') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Une campagne déjà envoyée ne peut pas être modifiée.');
        }
        
        $clients = Client::where('user_id', Auth::id())->get();
        
        return view('campaigns.edit', compact('campaign', 'clients'));
    }

    /**
     * Mettre à jour une campagne
     */
    public function update(Request $request, Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Ne pas autoriser la modification des campagnes envoyées
        if ($campaign->status === 'sent') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Une campagne déjà envoyée ne peut pas être modifiée.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:birthday,holiday,promotion,custom',
            'message' => 'required|string|max:160',
            'target_segments' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);
        
        $campaign->name = $validated['name'];
        $campaign->type = $validated['type'];
        $campaign->message = $validated['message'];
        $campaign->status = $request->has('scheduled_at') ? 'scheduled' : 'draft';
        $campaign->scheduled_at = $request->filled('scheduled_at') ? $validated['scheduled_at'] : null;
        $campaign->target_segments = $request->filled('target_segments') ? $validated['target_segments'] : null;
        $campaign->save();
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campagne mise à jour avec succès.');
    }

    /**
     * Supprimer une campagne
     */
    public function destroy(Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Ne pas autoriser la suppression des campagnes envoyées
        if ($campaign->status === 'sent') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Une campagne déjà envoyée ne peut pas être supprimée.');
        }
        
        $campaign->delete();
        
        return redirect()->route('campaigns.index')
            ->with('success', 'Campagne supprimée avec succès.');
    }

    /**
     * Envoyer une campagne (préparation)
     */
    public function prepare(Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Vérifier qu'on ne peut pas envoyer une campagne déjà envoyée
        if ($campaign->status === 'sent') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Cette campagne a déjà été envoyée.');
        }
        
        // Récupérer l'abonnement actif
        $subscription = Auth::user()->activeSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour envoyer des campagnes.');
        }
        
        // Sélectionner les clients selon les segments ciblés
        $query = Client::where('user_id', Auth::id());
        
        if ($campaign->target_segments) {
            // Exemple de filtre par segment
            if (in_array('recent', $campaign->target_segments)) {
                $query->where('created_at', '>=', now()->subMonths(3));
            }
            
            if (in_array('birthday_this_month', $campaign->target_segments)) {
                $currentMonth = now()->format('m');
                $query->whereRaw("MONTH(birthday) = ?", [$currentMonth]);
            }
            
            // Autres filtres selon les segments...
        }
        
        $clients = $query->get();
        
        // Vérifier le quota SMS disponible
        $smsCount = count($clients);
        
        if ($subscription->sms_remaining < $smsCount) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Vous n\'avez pas assez de SMS disponibles pour cette campagne.');
        }
        
        return view('campaigns.prepare', compact('campaign', 'clients', 'subscription', 'smsCount'));
    }

    /**
     * Envoyer une campagne (exécution)
     */
    public function send(Request $request, Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Vérifier qu'on ne peut pas envoyer une campagne déjà envoyée
        if ($campaign->status === 'sent') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Cette campagne a déjà été envoyée.');
        }
        
        $subscription = Auth::user()->activeSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour envoyer des campagnes.');
        }
        
        // Sélectionner les clients selon les segments ciblés
        $query = Client::where('user_id', Auth::id());
        
        if ($campaign->target_segments) {
            // Appliquer les filtres comme dans la méthode prepare
            // ...
        }
        
        $clients = $query->get();
        $smsCount = count($clients);
        
        // Vérifier le quota SMS disponible
        if ($subscription->sms_remaining < $smsCount) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Vous n\'avez pas assez de SMS disponibles pour cette campagne.');
        }
        
        // Mise à jour de la campagne
        $campaign->status = 'sent';
        $campaign->sent_at = now();
        $campaign->sms_count = $smsCount;
        $campaign->save();
        
        // Décrémenter le quota SMS
        $subscription->sms_remaining -= $smsCount;
        $subscription->save();
        
        // Créer les messages promotionnels (en attente d'envoi)
        foreach ($clients as $client) {
            // Personnaliser le message si nécessaire
            $personalizedMessage = $this->personalizeMessage($campaign->message, $client);
            
            PromotionalMessage::create([
                'user_id' => Auth::id(),
                'campaign_id' => $campaign->id,
                'client_id' => $client->id,
                'message' => $personalizedMessage,
                'phone_number' => $client->phone,
                'status' => 'pending'
            ]);
        }
        
        // Ici, on simulerait l'envoi via Twilio ou autre API SMS
        // Pour l'instant, on marque simplement comme envoyé
        $campaign->messages()->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
        
        $campaign->sms_sent = $smsCount;
        $campaign->save();
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campagne envoyée avec succès à ' . $smsCount . ' clients.');
    }
    
    /**
     * Annuler une campagne programmée
     */
    public function cancel(Campaign $campaign)
    {
        // Vérifier que l'utilisateur est propriétaire de la campagne
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Vérifier que la campagne est bien programmée
        if ($campaign->status !== 'scheduled') {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Seules les campagnes programmées peuvent être annulées.');
        }
        
        // Mettre à jour le statut de la campagne
        $campaign->status = 'draft';
        $campaign->scheduled_at = null;
        $campaign->save();
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'La campagne programmée a été annulée avec succès.');
    }
    
    /**
     * Personnaliser un message avec les informations du client
     */
    private function personalizeMessage($message, $client)
    {
        $message = str_replace('{nom}', $client->name, $message);
        $message = str_replace('{prenom}', $client->first_name ?? '', $message);
        
        return $message;
    }
}
