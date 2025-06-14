<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Phone;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients.
     */
    public function index(Request $request)
    {
        $query = Client::withCount('bills');

        // Filtrer les clients par boutique pour les non-administrateurs
        if (!Gate::allows('admin')) {
//            $shopIds = Auth::user()->shops->pluck('id')->toArray();
//
//            // Trouver les clients qui ont des factures dans ces boutiques
//            $query->whereHas('bills', function($q) use ($shopIds) {
//                $q->whereIn('shop_id', $shopIds);
//            });
        }

        // Recherche par nom ou email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        // Filtrer par boutique spécifique si demandé
        if ($request->filled('shop_id')) {
            $shopId = $request->input('shop_id');
            $query->whereHas('bills', function($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            });
        }

        // Utiliser get() au lieu de paginate() pour récupérer tous les éléments
        $clients = $query->latest()->get();

        // Récupérer les boutiques pour le filtre
        $shops = Gate::allows('admin')
            ? \App\Models\Shop::all()
            : Auth::user()->shops;

        return view('clients.index', compact('clients', 'shops'));
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
            'notes' => $validated['notes'] ?? null,
            'user_id' => Auth::id(),
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

        // Enregistrer l'activité de création de client
        ActivityLogger::logCreated($client, "Client {$client->name} créé par " . Auth::user()?->name);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client créé avec succès');
    }

    public function show(Client $client)
    {
        $client->load(['phones', 'bills' => function($query) {
            $query->latest()->with(['items.product', 'products']);
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

        // Factures avec pagination
        $paginatedBills = $client->bills()->with(['products', 'items.product'])->latest()->paginate(10);

        return view('clients.show', compact('client', 'stats', 'monthlyStats', 'paginatedBills'));
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

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $client->getOriginal();

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

        // Enregistrer l'activité de mise à jour
        ActivityLogger::logUpdated($client, $oldValues, "Client {$client->name} modifié par " . Auth::user()?->name);

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

        // Enregistrer l'activité de suppression avant de supprimer le client
        ActivityLogger::logDeleted($client, "Client {$client->name} supprimé par " . Auth::user()?->name);

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
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255'
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'user_id' => Auth::id(),
        ]);

        if (!empty($validated['phone'])) {
            $phone = Phone::firstOrCreate(['number' => $validated['phone']]);
            $client->phones()->attach($phone->id);
        }

        // Enregistrer l'activité de création rapide
        ActivityLogger::logCreated($client, "Client {$client->name} créé rapidement par " . Auth::user()?->name);

        return response()->json([
            'success' => true,
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'phones' => $client->phones->pluck('number')
            ],
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
        $bills = $client->bills()->latest()->with(['products', 'items.product'])->paginate(10);

        return view('clients.bills.index', compact('client', 'bills'));
    }

    /**
     * Affiche le formulaire pour créer une facture pour un client spécifique
     */
    public function billsCreate(Client $client)
    {
        $products = \App\Models\Product::orderBy('name')->get();
        return view('clients.bills.create', compact('client', 'products'));
    }

    /**
     * Importer des clients depuis un fichier CSV ou depuis des contacts mobiles
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // Valider les données reçues
        $validated = $request->validate([
            'clients' => 'required|array',
            'clients.*.name' => 'required|string|max:255',
            'clients.*.email' => 'nullable|email|max:255',
            'clients.*.phone' => 'nullable|string|max:20',
            'clients.*.address' => 'nullable|string|max:255',
            'clients.*.company' => 'nullable|string|max:255',
            'clients.*.notes' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        $importCount = 0;
        $errors = [];
        $source = $request->input('source', 'csv');

        // Traiter chaque client
        foreach ($validated['clients'] as $clientData) {
            try {
                // Traitement spécifique selon la source
                $processedData = $this->processClientDataBySource($clientData, $source);

                // Vérifier si le client existe déjà (par email ou téléphone s'il est fourni)
                $existingClient = null;
                if (!empty($processedData['email'])) {
                    $existingClient = Client::where('email', $processedData['email'])->first();
                } elseif (!empty($processedData['phone'])) {
                    $existingClient = Client::whereHas('phones', function($q) use ($processedData) {
                        $q->where('number', $processedData['phone']);
                    })->first();
                }

                if ($existingClient) {
                    // Mettre à jour le client existant
                    $existingClient->update([
                        'name' => $processedData['name'],
                        'email' => $processedData['email'] ?? $existingClient->email,
                        'address' => $processedData['address'] ?? $existingClient->address,
                        'notes' => $processedData['notes'] ?? $existingClient->notes,
                    ]);

                    // Ajouter le téléphone s'il n'existe pas déjà
                    if (!empty($processedData['phone'])) {
                        $phone = Phone::firstOrCreate(['number' => $processedData['phone']]);
                        if (!$existingClient->phones->contains($phone->id)) {
                            $existingClient->phones()->attach($phone->id);
                        }
                    }

                    $client = $existingClient;
                } else {
                    // Créer un nouveau client
                    $client = Client::create([
                        'name' => $processedData['name'],
                        'email' => $processedData['email'] ?? null,
                        'address' => $processedData['address'] ?? null,
                        'notes' => $processedData['notes'] ?? null,
                        'user_id' => Auth::id(),
                    ]);

                    // Ajouter le téléphone
                    if (!empty($processedData['phone'])) {
                        $phone = Phone::firstOrCreate(['number' => $processedData['phone']]);
                        $client->phones()->attach($phone->id);
                    }
                }

                $importCount++;
            } catch (\Exception $e) {
                // Enregistrer l'erreur pour ce client
                $errors[] = [
                    'client' => $clientData['name'] ?? 'Client inconnu',
                    'error' => $e->getMessage()
                ];
            }
        }

        // Retourner la réponse
        if (count($errors) === 0) {
            return response()->json([
                'success' => true,
                'imported' => $importCount,
                'message' => "Importation réussie de $importCount clients."
            ]);
        } else {
            return response()->json([
                'success' => $importCount > 0,
                'imported' => $importCount,
                'errors' => $errors,
                'message' => "Importation partielle: $importCount clients importés avec " . count($errors) . " erreurs."
            ]);
        }
    }

    /**
     * Traite les données du client en fonction de la source
     *
     * @param array $clientData Données du client
     * @param string $source Source de l'importation (csv, apple, android, excel, etc.)
     * @return array Données traitées
     */
    private function processClientDataBySource($clientData, $source)
    {
        switch ($source) {
            case 'apple':
                // Traitement spécifique pour les contacts Apple
                return [
                    'name' => $clientData['name'] ?? ($clientData['first_name'] ?? '') . ' ' . ($clientData['last_name'] ?? ''),
                    'email' => $clientData['email'] ?? $clientData['work_email'] ?? $clientData['home_email'] ?? null,
                    'phone' => $this->formatPhoneNumber($clientData['phone'] ?? $clientData['mobile'] ?? $clientData['iPhone'] ?? $clientData['work_phone'] ?? null),
                    'address' => $clientData['address'] ?? $clientData['home_address'] ?? $clientData['work_address'] ?? null,
                    'notes' => $clientData['notes'] ?? null,
                ];

            case 'android':
                // Traitement spécifique pour les contacts Android
                return [
                    'name' => $clientData['name'] ?? ($clientData['given_name'] ?? '') . ' ' . ($clientData['family_name'] ?? ''),
                    'email' => $clientData['email'] ?? $clientData['email_1'] ?? $clientData['email_2'] ?? null,
                    'phone' => $this->formatPhoneNumber($clientData['phone'] ?? $clientData['phone_1'] ?? $clientData['mobile'] ?? $clientData['cell'] ?? null),
                    'address' => $clientData['address'] ?? $clientData['address_1'] ?? null,
                    'notes' => $clientData['notes'] ?? $clientData['memo'] ?? null,
                ];

            case 'excel':
                // Traitement spécifique pour les fichiers Excel
                return [
                    'name' => $clientData['name'] ?? $clientData['nom'] ?? $clientData['nom_complet'] ?? null,
                    'email' => $clientData['email'] ?? $clientData['courriel'] ?? $clientData['mail'] ?? null,
                    'phone' => $this->formatPhoneNumber($clientData['phone'] ?? $clientData['telephone'] ?? $clientData['tel'] ?? $clientData['mobile'] ?? $clientData['portable'] ?? null),
                    'address' => $clientData['address'] ?? $clientData['adresse'] ?? null,
                    'notes' => $clientData['notes'] ?? $clientData['remarques'] ?? $clientData['commentaires'] ?? null,
                ];

            case 'csv':
            default:
                // Traitement par défaut (CSV et autres)
                return [
                    'name' => $clientData['name'] ?? null,
                    'email' => $clientData['email'] ?? null,
                    'phone' => $this->formatPhoneNumber($clientData['phone'] ?? null),
                    'address' => $clientData['address'] ?? null,
                    'notes' => $clientData['notes'] ?? null,
                ];
        }
    }

    /**
     * Formate un numéro de téléphone pour s'assurer qu'il est dans un format standard
     *
     * @param string|null $phoneNumber Numéro de téléphone à formater
     * @return string|null Numéro formaté ou null
     */
    private function formatPhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return null;
        }

        // Supprimer tous les caractères non numériques sauf le + au début
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Si le numéro commence par un 0 et n'a pas de +, ajouter l'indicatif +33 (France) et supprimer le 0 initial
        if (substr($cleaned, 0, 1) === '0' && substr($cleaned, 0, 1) !== '+') {
            $cleaned = '+33' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Générer un modèle CSV pour l'importation de clients
     *
     * @return \Illuminate\Http\Response
     */
    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=modele-import-clients.csv',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, ['name', 'email', 'phone', 'address', 'company', 'notes']);

            // Exemple de données
            fputcsv($file, [
                'Nom Client Exemple',
                'client@example.com',
                '+123456789',
                '123 Rue Exemple, Ville',
                'Société Exemple',
                'Notes supplémentaires'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporter les clients au format CSV
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $clients = Client::with('phones')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=clients-export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom',
                'Email',
                'Téléphones',
                'Genre',
                'Date de naissance',
                'Adresse',
                'Notes',
                'Date de création'
            ]);

            // Données
            foreach ($clients as $client) {
                $phones = $client->phones->pluck('number')->implode(', ');

                fputcsv($file, [
                    $client->id,
                    $client->name,
                    $client->email,
                    $phones,
                    $client->sex,
                    $client->birth ? $client->birth->format('Y-m-d') : '',
                    $client->address,
                    $client->notes,
                    $client->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
