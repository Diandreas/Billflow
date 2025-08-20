<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with(['bill', 'shop', 'user']);

        // Filtrer par boutique selon le rôle
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('bill', function($billQuery) use ($search) {
                      $billQuery->where('reference', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15)->withQueryString();

        // Récupérer les boutiques selon le rôle
        $shops = Auth::user()->role === 'admin' ? Shop::all() : Auth::user()->shops;

        // Statistiques rapides
        $totalPayments = $query->sum('amount');
        $monthlyPayments = (clone $query)->whereMonth('payment_date', now()->month)->sum('amount');

        $paymentMethods = Payment::getPaymentMethods();

        return view('payments.index', compact('payments', 'shops', 'paymentMethods', 'totalPayments', 'monthlyPayments'));
    }

    public function show(Payment $payment)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($payment->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        $payment->load(['bill', 'shop', 'user']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Ajouter un paiement à une facture
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $bill = Bill::findOrFail($validated['bill_id']);

        // Vérifier l'accès à la facture
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($bill->shop_id, $shopIds)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
        }

        // Vérifier que le montant ne dépasse pas le restant dû
        $remainingAmount = $bill->total - $bill->getTotalPaidAttribute();
        if ($validated['amount'] > $remainingAmount) {
            return response()->json([
                'error' => 'Le montant du paiement ne peut pas dépasser le montant restant dû (' . number_format($remainingAmount, 0, ',', ' ') . ' FCFA)'
            ], 422);
        }

        $payment = Payment::create([
            'reference' => Payment::generateReference(),
            'bill_id' => $validated['bill_id'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'],
            'user_id' => Auth::id(),
            'shop_id' => $bill->shop_id,
        ]);

        // Mettre à jour le statut de paiement de la facture
        $bill->updatePaymentStatus();

        ActivityLogger::logCreated($payment, "Paiement {$payment->reference} ajouté à la facture {$bill->reference}");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'payment' => $payment,
                'bill_status' => $bill->fresh()->payment_status,
                'remaining_amount' => $bill->fresh()->remaining_amount,
            ]);
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Paiement enregistré avec succès');
    }

    /**
     * Encaisser maintenant (paiement complet)
     */
    public function payNow(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($bill->shop_id, $shopIds)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
        }

        $remainingAmount = $bill->total - $bill->getTotalPaidAttribute();

        if ($remainingAmount <= 0) {
            return response()->json(['error' => 'Cette facture est déjà entièrement payée'], 422);
        }

        $payment = $bill->addPayment(
            $remainingAmount,
            $validated['payment_method'],
            $validated['notes']
        );

        ActivityLogger::logCreated($payment, "Encaissement complet de la facture {$bill->reference}");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Facture encaissée avec succès',
                'payment' => $payment,
                'bill_status' => $bill->fresh()->payment_status,
            ]);
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Facture encaissée avec succès');
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(Payment $payment)
    {
        // Vérifier l'accès
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($payment->shop_id, $shopIds)) {
                abort(403, 'Accès non autorisé');
            }
        }

        $bill = $payment->bill;

        ActivityLogger::logDeleted($payment, "Paiement {$payment->reference} supprimé");

        $payment->delete();

        // Mettre à jour le statut de paiement de la facture
        $bill->updatePaymentStatus();

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Paiement supprimé avec succès');
    }

    /**
     * Obtenir les factures impayées pour un client
     */
    public function getUnpaidBills(Request $request)
    {
        $clientId = $request->get('client_id');
        
        $query = Bill::where('client_id', $clientId)
            ->where('payment_status', '!=', 'paid');

        // Filtrer par boutique selon le rôle
        if (Auth::user()->role !== 'admin') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        $bills = $query->with(['client'])
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'reference' => $bill->reference,
                    'total' => $bill->total,
                    'paid_amount' => $bill->getTotalPaidAttribute(),
                    'remaining_amount' => $bill->getRemainingAmountAttribute(),
                    'formatted_remaining' => number_format($bill->getRemainingAmountAttribute(), 0, ',', ' ') . ' FCFA',
                ];
            });

        return response()->json(['bills' => $bills]);
    }
}