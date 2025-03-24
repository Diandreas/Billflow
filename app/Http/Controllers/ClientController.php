<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Phone;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filtre par abonnement
        if ($request->filled('subscription_id')) {
            $subscriptionId = $request->subscription_id;
            $query->whereHas('bills', function($q) use ($subscriptionId) {
                $q->whereHas('user', function($u) use ($subscriptionId) {
                    $u->whereHas('subscriptions', function($s) use ($subscriptionId) {
                        $s->where('id', $subscriptionId);
                    });
                });
            });
        }
        
        // Ordre par défaut
        $query->orderBy('name');
        
        $clients = $query->withCount('bills')
                         ->withSum('bills as total_spent', 'total')
                         ->paginate(10)
                         ->withQueryString();
                         
        // Si on vient d'un abonnement spécifique, ajouter l'info pour l'affichage
        $subscription = null;
        if ($request->filled('subscription_id')) {
            $subscription = \App\Models\Subscription::with('plan')->find($request->subscription_id);
        }
        
        return view('clients.index', compact('clients', 'subscription'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'nullable|in:M,F,Other',
            'birth' => 'nullable|date',
            'phones' => 'nullable|array',
            'phones.*' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'sex' => $validated['sex'],
            'birth' => $validated['birth'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        // Gestion des numéros de téléphone
        if (!empty($validated['phones'])) {
            foreach ($validated['phones'] as $phoneNumber) {
                if (!empty($phoneNumber)) {
                    $phone = Phone::firstOrCreate(['number' => $phoneNumber]);
                    $client->phones()->attach($phone->id);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client->load('phones'),
                'message' => 'Client créé avec succès'
            ]);
        }

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client créé avec succès');
    }

    public function show(Client $client)
    {
        $client->load(['phones', 'bills' => function($query) {
            $query->latest()->with('products');
        }]);

        // Statistiques du client
        $stats = [
            'total_bills' => $client->bills->count(),
            'total_revenue' => $client->bills->sum('total'),
            'average_bill' => $client->bills->average('total'),
            'first_bill' => $client->bills->last()?->date,
            'last_bill' => $client->bills->first()?->date,
        ];

        // Évolution mensuelle
        $monthlyStats = $client->bills
            ->groupBy(function($bill) {
                return $bill->date->format('Y-m');
            })
            ->map(function($bills) {
                return [
                    'count' => $bills->count(),
                    'total' => $bills->sum('total'),
                    'average' => $bills->average('total')
                ];
            });

        return view('clients.show', compact('client', 'stats', 'monthlyStats'));
    }

    public function edit(Client $client)
    {
        $client->load('phones');
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'nullable|in:M,F,Other',
            'birth' => 'nullable|date',
            'phones' => 'nullable|array',
            'phones.*' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $client->update([
            'name' => $validated['name'],
            'sex' => $validated['sex'],
            'birth' => $validated['birth'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        // Mise à jour des numéros de téléphone
        $client->phones()->detach();
        if (!empty($validated['phones'])) {
            foreach ($validated['phones'] as $phoneNumber) {
                if (!empty($phoneNumber)) {
                    $phone = Phone::firstOrCreate(['number' => $phoneNumber]);
                    $client->phones()->attach($phone->id);
                }
            }
        }

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client mis à jour avec succès');
    }

    public function destroy(Client $client)
    {
        // Vérifier si le client a des factures
        if ($client->bills()->exists()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un client ayant des factures'
                ], 422);
            }
            return back()->with('error', 'Impossible de supprimer un client ayant des factures');
        }

        // Supprimer les relations avec les téléphones
        $client->phones()->detach();

        // Supprimer le client
        $client->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client supprimé avec succès');
    }

    // API Endpoints pour les requêtes AJAX
    public function search(Request $request)
    {
        $query = $request->get('q');
        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhereHas('phones', function($q) use ($query) {
                $q->where('number', 'like', "%{$query}%");
            })
            ->with('phones')
            ->limit(10)
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'phones' => $client->phones->pluck('number'),
                    'bills_count' => $client->bills_count ?? 0
                ];
            });

        return response()->json($clients);
    }

    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255'
        ]);

        $client = Client::create([
            'name' => $validated['name']
        ]);

        if (!empty($validated['phone'])) {
            $phone = Phone::firstOrCreate(['number' => $validated['phone']]);
            $client->phones()->attach($phone->id);
        }

        return response()->json([
            'success' => true,
            'client' => $client->load('phones'),
            'message' => 'Client créé avec succès'
        ]);
    }

    public function bills(Client $client)
    {
        return response()->json(
            $client->bills()
                ->with('products')
                ->latest()
                ->paginate(5)
        );
    }

    /**
     * Affiche la liste des factures d'un client spécifique
     */
    public function billsIndex(Client $client)
    {
        $client->load(['bills' => function($query) {
            $query->latest()->with('products');
        }]);
        
        return view('clients.bills.index', compact('client'));
    }

    /**
     * Affiche le formulaire pour créer une facture pour un client spécifique
     */
    public function billsCreate(Client $client)
    {
        $products = \App\Models\Product::orderBy('name')->get();
        return view('clients.bills.create', compact('client', 'products'));
    }
}
