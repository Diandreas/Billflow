<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\ActivityLogger;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query()->with(['shop', 'user']);

        // Filtrer par boutique selon le rôle
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
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
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Récupérer les boutiques selon le rôle
        $shops = Auth::user()->role === 'admin' ? Shop::all() : Auth::user()->shops;

        // Statistiques rapides
        $totalExpenses = $query->sum('amount');
        $monthlyExpenses = (clone $query)->whereMonth('date', now()->month)->sum('amount');
        
        // Catégories pour les filtres
        $categories = Expense::getDefaultCategories();

        return view('expenses.index', compact('expenses', 'shops', 'categories', 'totalExpenses', 'monthlyExpenses'));
    }

    public function create()
    {
        $shops = Auth::user()->role === 'admin' ? Shop::all() : Auth::user()->shops;
        $defaultShop = $shops->first();
        
        $categories = $defaultShop ? 
            Expense::getCategoriesByBusinessType($defaultShop->business_type) : 
            Expense::getDefaultCategories();

        $paymentMethods = [
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
            'check' => 'Chèque',
        ];

        return view('expenses.create', compact('shops', 'categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'supplier' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'receipt' => 'nullable|image|max:5120', // 5MB max
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|string|in:weekly,monthly,yearly',
        ]);

        // Vérifier l'accès à la boutique
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($validated['shop_id'], $shopIds)) {
                return back()->withErrors(['shop_id' => 'Vous n\'avez pas accès à cette boutique.']);
            }
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $nextOccurrence = null;
        if ($validated['is_recurring'] && isset($validated['recurring_frequency'])) {
            $nextOccurrence = match($validated['recurring_frequency']) {
                'weekly' => now()->addWeek(),
                'monthly' => now()->addMonth(),
                'yearly' => now()->addYear(),
                default => null,
            };
        }

        $expense = Expense::create([
            'reference' => Expense::generateReference(),
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'supplier' => $validated['supplier'],
            'payment_method' => $validated['payment_method'],
            'description' => $validated['description'],
            'receipt_path' => $receiptPath,
            'shop_id' => $validated['shop_id'],
            'user_id' => Auth::id(),
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_frequency' => $validated['recurring_frequency'],
            'next_occurrence' => $nextOccurrence,
        ]);

        ActivityLogger::logCreated($expense, "Dépense {$expense->reference} créée");

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Dépense enregistrée avec succès');
    }

    public function show(Expense $expense)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($expense->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        $expense->load(['shop', 'user']);

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($expense->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        $shops = Auth::user()->role === 'admin' ? Shop::all() : Auth::user()->shops;
        $categories = Expense::getCategoriesByBusinessType($expense->shop->business_type);
        
        $paymentMethods = [
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement bancaire',
            'check' => 'Chèque',
        ];

        return view('expenses.edit', compact('expense', 'shops', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'supplier' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'receipt' => 'nullable|image|max:5120',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|string|in:weekly,monthly,yearly',
        ]);

        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($validated['shop_id'], $shopIds) || !in_array($expense->shop_id, $shopIds)) {
                return back()->withErrors(['shop_id' => 'Vous n\'avez pas accès à cette boutique.']);
            }
        }

        $oldValues = $expense->getOriginal();

        $receiptPath = $expense->receipt_path;
        if ($request->hasFile('receipt')) {
            // Supprimer l'ancien fichier
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $nextOccurrence = $expense->next_occurrence;
        if ($validated['is_recurring'] && isset($validated['recurring_frequency'])) {
            $nextOccurrence = match($validated['recurring_frequency']) {
                'weekly' => now()->addWeek(),
                'monthly' => now()->addMonth(),
                'yearly' => now()->addYear(),
                default => null,
            };
        } elseif (!$validated['is_recurring']) {
            $nextOccurrence = null;
        }

        $expense->update([
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'supplier' => $validated['supplier'],
            'payment_method' => $validated['payment_method'],
            'description' => $validated['description'],
            'receipt_path' => $receiptPath,
            'shop_id' => $validated['shop_id'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_frequency' => $validated['recurring_frequency'],
            'next_occurrence' => $nextOccurrence,
        ]);

        ActivityLogger::logUpdated($expense, $oldValues, "Dépense {$expense->reference} modifiée");

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Dépense mise à jour avec succès');
    }

    public function destroy(Expense $expense)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($expense->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        // Supprimer le fichier de justificatif
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        ActivityLogger::logDeleted($expense, "Dépense {$expense->reference} supprimée");

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense supprimée avec succès');
    }

    /**
     * Saisie rapide de dépense (modal/AJAX)
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
        ]);

        // Vérifier l'accès à la boutique
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($validated['shop_id'], $shopIds)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
        }

        $expense = Expense::create([
            'reference' => Expense::generateReference(),
            'date' => now(),
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'payment_method' => $validated['payment_method'],
            'description' => $validated['description'],
            'shop_id' => $validated['shop_id'],
            'user_id' => Auth::id(),
        ]);

        ActivityLogger::logCreated($expense, "Dépense rapide {$expense->reference} créée");

        return response()->json([
            'success' => true,
            'message' => 'Dépense enregistrée avec succès',
            'expense' => $expense,
        ]);
    }

    /**
     * Obtenir les catégories par type de business
     */
    public function getCategories(Request $request)
    {
        $shopId = $request->get('shop_id');
        $shop = Shop::find($shopId);
        
        if (!$shop) {
            return response()->json(['categories' => Expense::getDefaultCategories()]);
        }

        $categories = Expense::getCategoriesByBusinessType($shop->business_type);
        
        return response()->json(['categories' => $categories]);
    }
}