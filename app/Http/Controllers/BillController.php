<?php

// app/Http/Controllers/BillController.php
namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Client;
use App\Models\Product;
use App\Models\Phone;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['client', 'products'])
            ->latest()
            ->paginate(10);
        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::all();
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

        $bill = Bill::create([
            'reference' => Bill::generateReference(),
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'],
            'client_id' => $validated['client_id'],
            'user_id' => auth()->id(),
        ]);

        foreach ($validated['products'] as $product) {
            $bill->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'total' => $product['quantity'] * $product['unit_price']
            ]);
        }

        $bill->calculateTotals();

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Facture créée avec succès');
    }

    public function show(Bill $bill)
    {
        $bill->load(['client', 'products', 'user']);
        return view('bills.show', compact('bill'));
    }

    public function exportPdf(Bill $bill)
    {
        $bill->load(['client', 'products', 'user']);
        $settings = Setting::getSettings();

        $pdf = PDF::loadView('bills.pdf', compact('bill', 'settings'));
        return $pdf->download('facture-' . $bill->reference . '.pdf');
    }

    // API endpoints pour la création dynamique
    public function getProductDetails(Product $product)
    {
        return response()->json($product);
    }
}
