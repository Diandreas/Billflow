<?php
namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Client;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Shop;
use App\Models\Commission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Gate;
use App\Models\BillItem;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::query()
            ->with(['client', 'seller', 'shop']);

        // Filtrer par boutique si l'utilisateur n'est pas admin
        if (!Gate::allows('admin')) {
            $shops = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shops);
        }

        // Filtrer par boutique spécifique si demandé
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->input('shop_id'));
        }

        // Filtrer par vendeur si demandé
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->input('seller_id'));
        }

        // Filtrer par client si demandé
        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        // Filtrer par prix spécifique
        if ($request->has('unit_price')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('unit_price', $request->input('unit_price'));
            });
        }

        // Filtrer par date
        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        // Filtrer par factures nécessitant une approbation
        if ($request->has('approval') && $request->input('approval') === 'needed') {
            $query->where('needs_approval', true)
                  ->where('approved', false);
        }

        $bills = $query->orderBy('date', 'desc')->paginate(15);

        $shops = Gate::allows('admin') 
            ? Shop::all() 
            : Auth::user()->shops;
            
        $sellers = User::where('role', 'vendeur')->get();
        $clients = Client::all();

        // Récupérer la liste des prix uniques pour le filtre
        $uniquePrices = BillItem::select('unit_price')
            ->distinct()
            ->orderBy('unit_price')
            ->pluck('unit_price');

        return view('bills.index', compact('bills', 'shops', 'sellers', 'clients', 'uniquePrices'));
    }

    public function create()
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('create-bill')) {
            abort(403, 'Action non autorisée.');
        }

        // Pour les vendeurs, ne montrer que les clients de leur boutique
        if (Auth::user()->role === 'vendeur') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $clients = Client::whereHas('bills', function ($query) use ($shopIds) {
                $query->whereIn('shop_id', $shopIds);
            })->orderBy('name')->get();
        } else {
            $clients = Client::orderBy('name')->get();
        }

        $products = Product::where('stock_quantity', '>', 0)->get();
        
        // Obtenir les boutiques de l'utilisateur actuel
        $shops = Gate::allows('admin') 
            ? Shop::all() 
            : Auth::user()->shops;
        
        // Obtenir les vendeurs par boutique
        $shopVendors = [];
        foreach ($shops as $shop) {
            $shopVendors[$shop->id] = $shop->users()
                ->where('role', 'vendeur')
                ->get()
                ->toArray();
        }

        return view('bills.create', compact('clients', 'products', 'shops', 'shopVendors'));
    }

    public function store(Request $request)
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('create-bill')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shop_id' => 'required|exists:shops,id',
            'seller_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'payment_method' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        // Vérifier que l'utilisateur a accès à la boutique
        if (!Gate::allows('access-shop', Shop::find($request->shop_id))) {
            return back()->with('error', 'Vous n\'avez pas accès à cette boutique');
        }

        // Calculer le total et la TVA
        $total = 0;
        foreach ($validated['products'] as $product) {
            $total += $product['price'] * $product['quantity'];
        }

        $taxAmount = $total * ($validated['tax_rate'] / 100);
        $totalWithTax = $total + $taxAmount;

        // Générer un numéro de référence unique
        $reference = 'FACT-' . date('YmdHis') . '-' . Auth::id();

        // Créer la facture
        $bill = Bill::create([
            'reference' => $reference,
            'description' => 'Facture pour ' . Client::find($validated['client_id'])->name,
            'total' => $totalWithTax,
            'date' => $validated['date'],
            'due_date' => $validated['due_date'],
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'status' => 'En attente',
            'payment_method' => $validated['payment_method'],
            'comments' => $validated['comments'],
            'user_id' => Auth::id(),
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'seller_id' => $validated['seller_id'],
            'reprint_count' => 0,
        ]);

        // Ajouter les produits à la facture
        foreach ($validated['products'] as $productData) {
            $product = Product::find($productData['id']);
            
            // Vérifier le stock
            if ($product->stock_quantity < $productData['quantity']) {
                return back()->with('error', "Stock insuffisant pour {$product->name}");
            }
            
            // Ajouter le produit à la facture
            BillItem::create([
                'bill_id' => $bill->id,
                'product_id' => $product->id,
                'unit_price' => $productData['price'],
                'quantity' => $productData['quantity'],
                'price' => $productData['price'],
                'total' => $productData['price'] * $productData['quantity'],
            ]);
            
            // Mettre à jour le stock
            $product->stock_quantity -= $productData['quantity'];
            $product->save();
            
            // Créer un mouvement d'inventaire
            $product->inventoryMovements()->create([
                'type' => 'vente',
                'quantity' => -$productData['quantity'],
                'reference' => $reference,
                'bill_id' => $bill->id,
                'user_id' => Auth::id(),
                'unit_price' => $productData['price'],
                'total_price' => $productData['price'] * $productData['quantity'],
                'stock_before' => $product->stock_quantity + $productData['quantity'],
                'stock_after' => $product->stock_quantity,
            ]);
        }

        // Calculer et créer la commission du vendeur
        $seller = User::find($validated['seller_id']);
        if ($seller && $seller->commission_rate > 0) {
            $commissionAmount = $totalWithTax * ($seller->commission_rate / 100);
            
            Commission::create([
                'user_id' => $seller->id,
                'bill_id' => $bill->id,
                'amount' => $commissionAmount,
                'rate' => $seller->commission_rate,
                'base_amount' => $totalWithTax,
                'type' => 'vente',
                'is_paid' => false,
            ]);
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Facture créée avec succès');
    }

    public function show(Bill $bill)
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('edit-bill', $bill)) {
            abort(403, 'Action non autorisée.');
        }

        $bill->load(['client', 'seller', 'shop', 'items.product']);

        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('edit-bill', $bill)) {
            abort(403, 'Action non autorisée.');
        }

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

        // Sauvegarder les anciens produits pour les restaurer en stock si nécessaire
        $oldProducts = $bill->products->mapWithKeys(function ($product) {
            return [$product->id => $product->pivot->quantity];
        })->toArray();
        
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
                
                // Gestion du stock pour les produits modifiés
                $product = Product::find($productId);
                if ($product && $product->type === 'physical') {
                    $oldQuantity = $oldProducts[$productId] ?? 0;
                    
                    // Si le produit était déjà dans la facture, on ajuste la différence
                    if (array_key_exists($productId, $oldProducts)) {
                        $quantityDiff = $quantity - $oldQuantity;
                        
                        if ($quantityDiff > 0) {
                            // Retirer la différence supplémentaire du stock
                            InventoryMovement::createExit(
                                $productId,
                                $quantityDiff,
                                $price,
                                'Facture modifiée: ' . $bill->reference,
                                $bill->id,
                                'Ajustement après modification de facture'
                            );
                        } elseif ($quantityDiff < 0) {
                            // Remettre en stock la différence
                            InventoryMovement::createEntry(
                                $productId,
                                abs($quantityDiff),
                                $price,
                                'Facture modifiée: ' . $bill->reference,
                                'Ajustement après modification de facture'
                            );
                        }
                        
                        // Supprimer de la liste des anciens produits
                        unset($oldProducts[$productId]);
                    } else {
                        // Nouveau produit ajouté à la facture
                        InventoryMovement::createExit(
                            $productId,
                            $quantity,
                            $price,
                            'Facture modifiée: ' . $bill->reference,
                            $bill->id,
                            'Nouveau produit ajouté à la facture'
                        );
                    }
                }
            }
        }
        
        // Remettre en stock les produits qui ont été supprimés de la facture
        foreach ($oldProducts as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product && $product->type === 'physical') {
                InventoryMovement::createEntry(
                    $productId,
                    $quantity,
                    null,
                    'Facture modifiée: ' . $bill->reference,
                    'Produit retiré de la facture'
                );
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

    /**
     * Mettre à jour le statut d'une facture
     */
    public function updateStatus(Request $request, Bill $bill)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        $oldStatus = $bill->status;
        $newStatus = $request->status;
        
        // Mettre à jour le statut
        $bill->update(['status' => $newStatus]);
        
        // Messages personnalisés selon le changement de statut
        if ($oldStatus !== $newStatus) {
            if ($newStatus === 'paid') {
                $message = 'La facture a été marquée comme payée.';
            } elseif ($newStatus === 'pending') {
                $message = 'La facture a été marquée comme en attente de paiement.';
            } elseif ($newStatus === 'cancelled') {
                $message = 'La facture a été annulée.';
            }
        } else {
            $message = 'Le statut de la facture a été mis à jour.';
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        
        return redirect()
            ->route('bills.show', $bill)
            ->with('success', $message);
    }

    public function downloadPdf(Bill $bill)
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('edit-bill', $bill)) {
            abort(403, 'Action non autorisée.');
        }

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

    /**
     * Imprimer une facture avec QR code.
     */
    public function print(Bill $bill)
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('print-bill', $bill)) {
            abort(403, 'Action non autorisée.');
        }

        // Charger les relations nécessaires
        $bill->load(['client', 'products', 'shop', 'seller', 'user']);

        // Mettre à jour le compteur d'impression
        $bill->increment('print_count');
        $bill->update(['last_printed_at' => now()]);

        // Générer le code QR pour la vérification
        $verificationUrl = route('bills.verify', ['code' => base64_encode($bill->reference)]);
        $qrCode = QrCode::size(150)->generate($verificationUrl);

        // Récupérer les paramètres de l'entreprise
        $company = Setting::where('key', 'company')->first() ? json_decode(Setting::where('key', 'company')->first()->value, true) : null;

        return view('bills.print', compact('bill', 'qrCode', 'company'));
    }

    /**
     * Ajouter une signature à la facture.
     */
    public function addSignature(Request $request, Bill $bill)
    {
        // Vérifier les autorisations
        $this->authorize('update', $bill);
        
        $validated = $request->validate([
            'signature' => 'required|string',
        ]);
        
        // Décoder l'image base64
        $image = $request->get('signature');
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        // Générer un nom de fichier unique
        $filename = 'signatures/' . uniqid() . '.png';
        
        // Sauvegarder l'image
        Storage::disk('public')->put($filename, base64_decode($image));
        
        // Mettre à jour la facture
        $bill->signature_path = $filename;
        $bill->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * Vérifier l'authenticité d'une facture par QR code.
     */
    public function verifyQrCode(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|json',
        ]);
        
        $data = json_decode($request->data, true);
        
        if (!isset($data['reference'])) {
            return response()->json([
                'valid' => false,
                'message' => 'QR code invalide ou données manquantes'
            ]);
        }
        
        $bill = Bill::where('reference', $data['reference'])->first();
        
        if (!$bill) {
            return response()->json([
                'valid' => false,
                'message' => 'Facture non trouvée'
            ]);
        }
        
        // Vérifier si les données du QR code correspondent aux données de la facture
        $isValid = 
            $data['date'] == $bill->date->format('Y-m-d H:i:s') &&
            $data['total'] == $bill->total &&
            $data['client'] == $bill->client->name &&
            $data['shop'] == $bill->shop->name &&
            $data['seller'] == $bill->seller->name;
        
        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Facture authentique' : 'Donnée de facture non concordantes',
            'bill' => $isValid ? [
                'reference' => $bill->reference,
                'date' => $bill->date->format('d/m/Y H:i'),
                'total' => number_format($bill->total, 2) . ' XAF',
                'client' => $bill->client->name,
                'shop' => $bill->shop->name,
                'seller' => $bill->seller->name,
                'status' => $bill->status,
            ] : null
        ]);
    }

    // Ajouter la nouvelle méthode pour afficher les factures par prix
    public function byPrice($price)
    {
        $query = Bill::query()
            ->with(['client', 'seller', 'shop'])
            ->whereHas('items', function($q) use ($price) {
                $q->where('unit_price', $price);
            });

        // Filtrer par boutique si l'utilisateur n'est pas admin
        if (!Gate::allows('admin')) {
            $shops = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shops);
        }

        $bills = $query->orderBy('date', 'desc')->paginate(15);

        $shops = Gate::allows('admin') 
            ? Shop::all() 
            : Auth::user()->shops;
            
        $sellers = User::where('role', 'vendeur')->get();
        $clients = Client::all();

        // Récupérer la liste des prix uniques pour le filtre
        $uniquePrices = BillItem::select('unit_price')
            ->distinct()
            ->orderBy('unit_price')
            ->pluck('unit_price');

        $filterPrice = $price;

        return view('bills.index', compact('bills', 'shops', 'sellers', 'clients', 'uniquePrices', 'filterPrice'));
    }

    // Ajouter la méthode pour approuver une facture
    public function approve(Request $request, Bill $bill)
    {
        // Vérifier que l'utilisateur a les droits pour approuver (admin ou gestionnaire)
        if (!Gate::allows('approve-bill')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string'
        ]);

        $bill->update([
            'approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null
        ]);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Facture approuvée avec succès');
    }
}
