<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Client;
use App\Models\Product;
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

        // Récupération des factures paginées
        $bills = $query->paginate(10)->withQueryString();

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
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Générer une référence unique
        $reference = Bill::generateReference();

        // Créer la facture
        $bill = Bill::create([
            'reference' => $reference,
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'],
            'user_id' => auth()->id(),
        ]);

        // Ajouter les produits
        foreach ($validated['products'] as $product) {
            $bill->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total' => $product['quantity'] * $product['unit_price']
            ]);
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
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Mettre à jour la facture
        $bill->update([
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'],
        ]);

        // Mettre à jour les produits
        $bill->products()->detach();
        foreach ($validated['products'] as $product) {
            $bill->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total' => $product['quantity'] * $product['unit_price']
            ]);
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
        $pdf = PDF::loadView('bills.pdf', compact('bill'));
        return $pdf->download("facture-{$bill->reference}.pdf");
    }
}
