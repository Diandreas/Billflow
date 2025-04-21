<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Bill;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['user', 'deliveryAgent', 'bill.client']);

        // Filtres
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('delivery_agent_id')) {
            $query->where('delivery_agent_id', $request->input('delivery_agent_id'));
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Dates
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }

        // Non-admins ne voient que leurs livraisons ou celles qu'ils ont assignées
        if (!Gate::allows('admin')) {
            $query->where(function($q) {
                $q->where('user_id', Auth::id())
                  ->orWhere('delivery_agent_id', Auth::id());
            });
        }

        $deliveries = $query->orderBy('created_at', 'desc')->paginate(15);

        $deliveryAgents = User::where('role', 'vendeur')->orderBy('name')->get();

        return view('deliveries.index', compact('deliveries', 'deliveryAgents'));
    }

    public function create()
    {
        $bills = Bill::where('status', 'paid')
            ->whereDoesntHave('delivery')
            ->with('client')
            ->orderBy('date', 'desc')
            ->get();

        $deliveryAgents = User::where('role', 'vendeur')->orderBy('name')->get();

        return view('deliveries.create', compact('bills', 'deliveryAgents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bill_id' => 'nullable|exists:bills,id',
            'delivery_agent_id' => 'required|exists:users,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'delivery_fee' => 'required|numeric|min:0',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        // Si facture spécifiée, récupérer client et adresse automatiquement
        if (!empty($validated['bill_id'])) {
            $bill = Bill::with('client')->find($validated['bill_id']);
            if ($bill) {
                $validated['recipient_name'] = $bill->client->name;
                $validated['recipient_phone'] = $bill->client->phones->first()->number ?? '';
            }
        }

        // Créer la livraison
        $delivery = Delivery::create([
            'reference' => Delivery::generateReference(),
            'bill_id' => $validated['bill_id'] ?? null,
            'user_id' => Auth::id(),
            'delivery_agent_id' => $validated['delivery_agent_id'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_fee' => $validated['delivery_fee'],
            'scheduled_at' => $validated['scheduled_at'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'payment_status' => isset($validated['amount_paid']) && $validated['amount_paid'] >= $validated['total_amount'] ? 'paid' : 'unpaid',
            'total_amount' => $validated['total_amount'],
            'amount_paid' => $validated['amount_paid'] ?? 0,
        ]);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Livraison créée avec succès');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['user', 'deliveryAgent', 'bill.client']);

        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'Impossible de modifier une livraison qui n\'est pas en attente');
        }

        $delivery->load(['user', 'deliveryAgent', 'bill.client']);

        $bills = Bill::where('status', 'paid')
            ->where(function($query) use ($delivery) {
                $query->whereDoesntHave('delivery')
                      ->orWhereHas('delivery', function($q) use ($delivery) {
                          $q->where('id', $delivery->id);
                      });
            })
            ->with('client')
            ->orderBy('date', 'desc')
            ->get();

        $deliveryAgents = User::where('role', 'vendeur')->orderBy('name')->get();

        return view('deliveries.edit', compact('delivery', 'bills', 'deliveryAgents'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'Impossible de modifier une livraison qui n\'est pas en attente');
        }

        $validated = $request->validate([
            'bill_id' => 'nullable|exists:bills,id',
            'delivery_agent_id' => 'required|exists:users,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'delivery_fee' => 'required|numeric|min:0',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        // Mettre à jour les champs
        $delivery->update([
            'bill_id' => $validated['bill_id'] ?? null,
            'delivery_agent_id' => $validated['delivery_agent_id'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_fee' => $validated['delivery_fee'],
            'scheduled_at' => $validated['scheduled_at'],
            'notes' => $validated['notes'] ?? null,
            'total_amount' => $validated['total_amount'],
            'amount_paid' => $validated['amount_paid'] ?? $delivery->amount_paid,
        ]);

        // Mettre à jour le statut de paiement si nécessaire
        if ($delivery->amount_paid >= $delivery->total_amount) {
            $delivery->update(['payment_status' => 'paid']);
        }

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Livraison mise à jour avec succès');
    }

    public function destroy(Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'Impossible de supprimer une livraison qui n\'est pas en attente');
        }

        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Livraison supprimée avec succès');
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_transit,delivered,cancelled'
        ]);

        if ($validated['status'] === 'delivered' && $delivery->status !== 'delivered') {
            $delivery->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);
        } else {
            $delivery->update([
                'status' => $validated['status']
            ]);
        }

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Statut de la livraison mis à jour avec succès');
    }

    public function markDelivered(Request $request, Delivery $delivery)
    {
        if ($delivery->status === 'delivered') {
            return redirect()->route('deliveries.show', $delivery)
                ->with('error', 'Cette livraison est déjà marquée comme livrée');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'notes' => $delivery->notes . "\n" . ($validated['notes'] ?? ''),
        ]);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Livraison marquée comme livrée avec succès');
    }

    public function recordPayment(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $newAmountPaid = $delivery->amount_paid + $validated['amount'];
        $newPaymentStatus = $newAmountPaid >= $delivery->total_amount ? 'paid' : 'unpaid';

        $delivery->update([
            'amount_paid' => $newAmountPaid,
            'payment_status' => $newPaymentStatus,
            'notes' => $delivery->notes . "\n" . "Paiement reçu: " . $validated['amount'] . " le " . now()->format('d/m/Y H:i') . "\n" . ($validated['notes'] ?? ''),
        ]);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Paiement enregistré avec succès');
    }
} 