<?php
namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Client;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        // Récupération des clients pour le filtre
        $clients = Client::orderBy('name')->get();

        // Requête de base pour les factures
        $query = Bill::with(['client', 'products'])->latest();

        // Application des filtres
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            $query->when($request->period, function ($q, $period) {
                return match($period) {
                    'current_month' => $q->whereMonth('date', now()->month),
                    'last_month' => $q->whereMonth('date', now()->subMonth()->month),
                    'current_year' => $q->whereYear('date', now()->year),
                    default => $q
                };
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('min_amount')) {
            $query->where('total', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('total', '<=', $request->max_amount);
        }

        // Récupération des factures paginées
        $bills = $query->paginate(10);
        
        // Statistiques pour la période filtrée
        $stats = [
            'count' => $query->count(),
            'total' => $query->sum('total'),
            'average' => $query->avg('total'),
        ];

        return view('bills.index', compact('bills', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('bills.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'tax_rate' => 'required|numeric',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'prices' => 'required|array',
        ]);

        // Générer une référence unique
        $reference = Bill::generateReference();

        // Créer la facture
        $bill = Bill::create([
            'reference' => $reference,
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'] ?? null,
            'user_id' => 1, // Utilisateur par défaut
            'status' => 'pending',
        ]);

        // Ajouter les produits
        $products = $request->input('products', []);
        $quantities = $request->input('quantities', []);
        $prices = $request->input('prices', []);

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products[$i]) && isset($quantities[$i]) && isset($prices[$i])) {
                $productId = $products[$i];
                $quantity = $quantities[$i];
                $price = $prices[$i];
                
                $bill->products()->attach($productId, [
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total' => $quantity * $price
                ]);
            }
        }

        // Calculer les totaux
        $bill->calculateTotals();

        return redirect()
            ->route('bills.show', $bill)
            ->with('success', 'Facture créée avec succès');
    }

    public function show(Bill $bill)
    {
        $bill->load(['client', 'products', 'user']);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $bill->load(['client', 'products']);

        return view('bills.edit', compact('bill', 'clients', 'products'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'tax_rate' => 'required|numeric',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'prices' => 'required|array',
            'status' => 'nullable|string|in:pending,paid,cancelled',
        ]);

        // Mettre à jour la facture
        $bill->update([
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? $bill->status,
        ]);

        // Mettre à jour les produits
        $bill->products()->detach();
        
        $products = $request->input('products', []);
        $quantities = $request->input('quantities', []);
        $prices = $request->input('prices', []);

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products[$i]) && isset($quantities[$i]) && isset($prices[$i])) {
                $productId = $products[$i];
                $quantity = $quantities[$i];
                $price = $prices[$i];
                
                $bill->products()->attach($productId, [
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total' => $quantity * $price
                ]);
            }
        }

        // Recalculer les totaux
        $bill->calculateTotals();

        return redirect()
            ->route('bills.show', $bill)
            ->with('success', 'Facture mise à jour avec succès');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()
            ->route('bills.index')
            ->with('success', 'Facture supprimée avec succès');
    }

    public function downloadPdf(Bill $bill)
    {
        $bill->load(['client', 'products', 'user']);
        $settings = Setting::first();
        
        // Conversion du chemin Storage en chemin réel pour l'accès du PDF
        if ($settings && $settings->logo_path) {
            $logoRealPath = storage_path('app/public/' . $settings->logo_path);
            $settings->logo_real_path = $logoRealPath;
        }
        
        $pdf = PDF::loadView('bills.pdf', compact('bill', 'settings'));
        return $pdf->download("facture-{$bill->reference}.pdf");
    }

    /**
     * Exporter les factures au format CSV
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $bills = Bill::with(['client', 'products'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=factures-export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($bills) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Référence',
                'Client',
                'Date',
                'Date d\'échéance',
                'Statut',
                'Sous-total',
                'Taxes',
                'Total',
                'Produits',
                'Notes',
                'Date de création'
            ]);
            
            // Données
            foreach ($bills as $bill) {
                $products = $bill->products->map(function($product) {
                    return $product->name . ' (' . $product->pivot->quantity . ' x ' . number_format($product->pivot->price, 2) . ')';
                })->implode('; ');
                
                fputcsv($file, [
                    $bill->id,
                    $bill->reference,
                    $bill->client ? $bill->client->name : 'Client inconnu',
                    $bill->date ? $bill->date->format('Y-m-d') : '',
                    $bill->due_date ? $bill->due_date->format('Y-m-d') : '',
                    $bill->status,
                    $bill->subtotal,
                    $bill->tax_amount,
                    $bill->total,
                    $products,
                    $bill->notes,
                    $bill->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
