<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Phone;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('phones')
            ->withCount('bills')
            ->withSum('bills', 'total');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('phones', function($q) use ($search) {
                        $q->where('number', 'like', "%{$search}%");
                    });
            });
        }

        // Tri
        if ($request->filled('sort')) {
            $sortField = $request->sort;
            $sortDirection = $request->direction ?? 'asc';

            if ($sortField === 'total_bills') {
                $query->orderBy('bills_sum_total', $sortDirection);
            } elseif ($sortField === 'bills_count') {
                $query->orderBy('bills_count', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->latest();
        }

        $clients = $query->paginate(10)->withQueryString();

        // Statistiques globales
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::has('bills')->count(),
            'total_revenue' => Client::withSum('bills', 'total')->get()->sum('bills_sum_total'),
            'average_revenue_per_client' => Client::has('bills')
                ->withSum('bills', 'total')
                ->get()
                ->average('bills_sum_total')
        ];

        return view('clients.index', compact('clients', 'stats'));
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
            return back()->with('error', 'Impossible de supprimer un client ayant des factures');
        }

        // Supprimer les relations avec les téléphones
        $client->phones()->detach();

        // Supprimer le client
        $client->delete();

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
}
