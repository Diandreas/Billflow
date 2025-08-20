<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Client;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\ActivityLogger;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::query()->with(['client', 'user', 'shop']);

        // Filtrer par boutique selon le rôle
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $quotes = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Récupérer les boutiques selon le rôle
        $shops = Auth::user()->role === 'admin' ? Shop::all() : Auth::user()->shops;
        $clients = Client::orderBy('name')->get();

        // Statistiques rapides
        $totalQuotes = $query->count();
        $pendingQuotes = (clone $query)->where('status', 'pending')->count();
        $convertedQuotes = (clone $query)->where('status', 'converted')->count();

        return view('quotes.index', compact('quotes', 'shops', 'clients', 'totalQuotes', 'pendingQuotes', 'convertedQuotes'));
    }

    public function create()
    {
        // Vérifier les autorisations
        if (!Gate::allows('create-bill')) {
            abort(403, 'Action non autorisée.');
        }

        $user = Auth::user();

        // Déterminer les boutiques accessibles
        if ($user->role === 'admin') {
            $shops = Shop::all();
        } else {
            $shops = $user->shops;
        }

        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('quotes.create', compact('shops', 'clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shop_id' => 'required|exists:shops,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after:date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        // Vérifier l'accès à la boutique
        if (!Gate::allows('access-shop', Shop::find($request->shop_id))) {
            return back()->with('error', 'Vous n\'avez pas accès à cette boutique');
        }

        // Calculer le total
        $subtotal = 0;
        foreach ($validated['products'] as $product) {
            $subtotal += $product['price'] * $product['quantity'];
        }

        $taxAmount = $subtotal * ($validated['tax_rate'] / 100);
        $total = $subtotal + $taxAmount;

        $quote = Quote::create([
            'reference' => Quote::generateReference(),
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'user_id' => Auth::id(),
            'date' => $validated['date'],
            'valid_until' => $validated['valid_until'],
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total' => $total,
            'description' => $validated['description'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        // Ajouter les produits au devis
        foreach ($validated['products'] as $productData) {
            $quote->items()->create([
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['price'],
                'price' => $productData['price'],
                'total' => $productData['price'] * $productData['quantity'],
            ]);
        }

        ActivityLogger::logCreated($quote, "Devis {$quote->reference} créé");

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Devis créé avec succès');
    }

    public function show(Quote $quote)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($quote->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        $quote->load(['client', 'user', 'shop', 'items.product', 'bill']);

        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        // Vérifier l'accès et que le devis n'est pas déjà converti
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($quote->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        if ($quote->status === 'converted') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Ce devis a déjà été converti en facture et ne peut plus être modifié');
        }

        $user = Auth::user();
        $shops = $user->role === 'admin' ? Shop::all() : $user->shops;
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $quote->load(['items.product']);

        return view('quotes.edit', compact('quote', 'shops', 'clients', 'products'));
    }

    public function update(Request $request, Quote $quote)
    {
        if ($quote->status === 'converted') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Ce devis a déjà été converti en facture et ne peut plus être modifié');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shop_id' => 'required|exists:shops,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after:date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,accepted,rejected',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        $oldValues = $quote->getOriginal();

        // Calculer le total
        $subtotal = 0;
        foreach ($validated['products'] as $product) {
            $subtotal += $product['price'] * $product['quantity'];
        }

        $taxAmount = $subtotal * ($validated['tax_rate'] / 100);
        $total = $subtotal + $taxAmount;

        $quote->update([
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'date' => $validated['date'],
            'valid_until' => $validated['valid_until'],
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total' => $total,
            'description' => $validated['description'],
            'notes' => $validated['notes'],
            'status' => $validated['status'],
        ]);

        // Mettre à jour les produits
        $quote->items()->delete();
        foreach ($validated['products'] as $productData) {
            $quote->items()->create([
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['price'],
                'price' => $productData['price'],
                'total' => $productData['price'] * $productData['quantity'],
            ]);
        }

        ActivityLogger::logUpdated($quote, $oldValues, "Devis {$quote->reference} modifié");

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Devis mis à jour avec succès');
    }

    public function destroy(Quote $quote)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($quote->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        if ($quote->status === 'converted') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Ce devis a été converti en facture et ne peut pas être supprimé');
        }

        ActivityLogger::logDeleted($quote, "Devis {$quote->reference} supprimé");

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Devis supprimé avec succès');
    }

    /**
     * Convertir le devis en facture
     */
    public function convertToBill(Quote $quote)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($quote->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        if ($quote->status === 'converted') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Ce devis a déjà été converti en facture');
        }

        if ($quote->isExpired()) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Ce devis a expiré et ne peut plus être converti');
        }

        $bill = $quote->convertToBill();

        ActivityLogger::logUpdated($quote, [], "Devis {$quote->reference} converti en facture {$bill->reference}");

        return redirect()->route('bills.show', $bill)
            ->with('success', "Devis converti en facture {$bill->reference} avec succès");
    }

    /**
     * Dupliquer un devis
     */
    public function duplicate(Quote $quote)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($quote->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        $newQuote = Quote::create([
            'reference' => Quote::generateReference(),
            'client_id' => $quote->client_id,
            'shop_id' => $quote->shop_id,
            'user_id' => Auth::id(),
            'date' => now(),
            'valid_until' => now()->addDays(30),
            'tax_rate' => $quote->tax_rate,
            'tax_amount' => $quote->tax_amount,
            'total' => $quote->total,
            'description' => $quote->description,
            'notes' => $quote->notes,
            'status' => 'pending',
        ]);

        // Copier les items
        foreach ($quote->items as $item) {
            $newQuote->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'price' => $item->price,
                'total' => $item->total,
            ]);
        }

        ActivityLogger::logCreated($newQuote, "Devis {$newQuote->reference} créé par duplication de {$quote->reference}");

        return redirect()->route('quotes.show', $newQuote)
            ->with('success', 'Devis dupliqué avec succès');
    }
}