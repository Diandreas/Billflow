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
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Gate;
use App\Models\BillItem;
use App\Models\Signature;
use App\Services\ActivityLogger;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::query()
            ->with(['client', 'seller', 'shop', 'items']);

        // Appliquer les filtres de rôle
        if (Auth::user()->role === 'vendeur') {
            // Le vendeur ne voit que les factures de sa boutique actuelle
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);

            // Si l'utilisateur est associé à plusieurs boutiques et qu'une boutique spécifique est sélectionnée
            if ($request->filled('shop_id') && in_array($request->input('shop_id'), $shopIds)) {
                $query->where('shop_id', $request->input('shop_id'));
            }
        } elseif (Auth::user()->role === 'manager') {
            // Le manager voit les factures des boutiques qu'il gère
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);

            // Si une boutique spécifique est sélectionnée
            if ($request->filled('shop_id') && in_array($request->input('shop_id'), $shopIds)) {
                $query->where('shop_id', $request->input('shop_id'));
            }
        } else {
            // L'admin voit tout
            if ($request->filled('shop_id')) {
                $query->where('shop_id', $request->input('shop_id'));
            }
        }

        // Filtrer par vendeur si demandé
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->input('seller_id'));
        }

        // Filtrer par client si demandé
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        // Filtrer par recherche textuelle
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('phones', function($phoneQuery) use ($search) {
                                $phoneQuery->where('number', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('shop', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seller', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtrer par période
        if ($request->filled('period')) {
            $period = $request->input('period');

            switch ($period) {
                case 'today':
                    $query->whereDate('date', now()->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                    break;
                case 'month':
                    $query->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
                    break;
                case 'quarter':
                    $query->whereBetween('date', [now()->startOfQuarter()->toDateString(), now()->endOfQuarter()->toDateString()]);
                    break;
                case 'year':
                    $query->whereBetween('date', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()]);
                    break;
                // La période personnalisée est gérée par les champs date_from et date_to
            }
        }

        // Filtrer par date spécifique
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        // Filtrer par prix spécifique
        if ($request->filled('unit_price')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('unit_price', $request->input('unit_price'));
            });
        }

        // Filtrer par factures nécessitant une approbation
        if ($request->filled('approval') && $request->input('approval') === 'needed') {
            $query->where('needs_approval', true)
                ->where('approved', false);
        }

        $bills = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Récupérer les boutiques selon le rôle de l'utilisateur
        $shops = Auth::user()->role === 'admin'
            ? Shop::all()
            : Auth::user()->shops;

        // Récupérer les vendeurs selon le rôle et les boutiques
        if (Auth::user()->role === 'admin') {
            $sellers = User::where('role', 'vendeur')->get();
        } elseif (Auth::user()->role === 'manager') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $sellers = User::whereHas('shops', function($q) use ($shopIds) {
                $q->whereIn('shop_id', $shopIds);
            })->where('role', 'vendeur')->get();
        } else {
            // Vendeur: seulement lui-même
            $sellers = User::where('id', Auth::id())->get();
        }

        // Récupérer les clients selon le rôle et les boutiques
        if (Auth::user()->role === 'admin') {
            $clients = Client::all();
        } else {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $clients = Client::whereHas('bills', function($q) use ($shopIds) {
                $q->whereIn('shop_id', $shopIds);
            })->get();
        }

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

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Déterminer la boutique selon le rôle de l'utilisateur
        if ($user->role === 'admin') {
            // Pour admin, on récupère toutes les boutiques
            $shops = Shop::all();
            // On prend la première boutique par défaut
            $defaultShopId = $shops->first() ? $shops->first()->id : null;
        } elseif ($user->role === 'manager') {
            // Pour manager, on récupère toutes leurs boutiques
            $shops = $user->shops;
            // On prend la première boutique par défaut
            $defaultShopId = $shops->first() ? $shops->first()->id : null;
        } else {
            // Pour un vendeur, on prend directement sa boutique
            $shopIds = $user->shops->pluck('id')->toArray();
            if (empty($shopIds)) {
                abort(403, 'Vous n\'êtes associé à aucune boutique.');
            }
            $shops = $user->shops;
            $defaultShopId = $shops->first()->id;
        }

        // Déterminer le vendeur
        if ($user->role === 'vendeur') {
            // Si l'utilisateur est un vendeur, c'est automatiquement lui le vendeur
            $sellers = collect([$user]);
            $defaultSellerId = $user->id;
        } elseif ($user->role === 'manager') {
            // Si manager, on récupère les vendeurs de ses boutiques
            $shopIds = $user->shops->pluck('id')->toArray();
            $sellers = User::whereHas('shops', function($q) use ($shopIds) {
                $q->whereIn('shop_id', $shopIds);
            })->where('role', 'vendeur')->get();
            $defaultSellerId = null;
        } else {
            // Si admin, il peut voir tous les vendeurs
            $sellers = User::where('role', 'vendeur')->get();
            $defaultSellerId = null;
        }
        $clients = Client::all();
        // Récupérer les clients selon le rôle
//        if ($user->role === 'admin') {
//            $clients = Client::all();
//        } else {
//            $shopIds = $user->shops->pluck('id')->toArray();
//            $clients = Client::whereHas('bills', function ($query) use ($shopIds) {
//                $query->whereIn('shop_id', $shopIds);
//            })->orderBy('name')->get();
//        }

        // Récupérer les produits en fonction du rôle
        // Les managers et admins peuvent voir tous les produits et services
        // Les vendeurs ne voient que les produits physiques avec du stock
        if ($user->role === 'admin' || $user->role === 'manager') {
            $products = Product::orderBy('name')->get();
        } else {
            // Vendeurs: seulement les produits physiques avec stock
            $products = Product::where(function($query) {
                $query->where('type', 'physical')
                      ->where('stock_quantity', '>', 0);
            })->orderBy('name')->get();
        }

        // Obtenir les vendeurs par boutique
        $shopVendors = [];
        foreach ($shops as $shop) {
            $shopVendors[$shop->id] = $shop->users()
                ->where('role', 'vendeur')
                ->get()
                ->toArray();
        }

        return view('bills.create', compact('clients', 'products', 'shops', 'shopVendors', 'sellers', 'defaultShopId', 'defaultSellerId'));
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
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'user_id' => Auth::id(),
            'seller_id' => $validated['seller_id'],
            'date' => $validated['date'],
            'due_date' => date('Y-m-d', strtotime($validated['date'] . ' + 30 days')),
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total' => $totalWithTax,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'comments' => $validated['comments'] ?? null,
        ]);

        // Enregistrer l'activité de création de facture
        ActivityLogger::logCreated($bill, "Facture {$bill->reference} créée par " . Auth::user()->name);

        // Ajouter les produits à la facture
        foreach ($validated['products'] as $product) {
            $productItem = Product::find($product['id']);
            $bill->items()->create([
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'unit_price' => $product['price'],
                'price' => $product['price'],
                'total' => $product['price'] * $product['quantity'],
            ]);

            // Si c'est un produit physique, mettre à jour le stock
            if ($productItem->type === 'physical') {
                $productItem->stock_quantity -= $product['quantity'];
                $productItem->save();

                // Créer un mouvement d'inventaire
                $productItem->inventoryMovements()->create([
                    'type' => 'vente',
                    'quantity' => -$product['quantity'],
                    'reference' => $reference,
                    'bill_id' => $bill->id,
                    'user_id' => Auth::id(),
                    'shop_id' => $validated['shop_id'],
                    'unit_price' => $product['price'],
                    'total_price' => $product['price'] * $product['quantity'],
                    'stock_before' => $productItem->stock_quantity + (int)$product['quantity'],
                    'stock_after' => $productItem->stock_quantity,
                ]);
            }
        }

        // Calculer et créer la commission du vendeur
        $seller = User::find($validated['seller_id']);
        if ($seller && $seller->commission_rate > 0) {
            Commission::calculateForBill($bill);
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Facture créée avec succès');
    }

    public function show(Bill $bill)
    {
        // Vérifier les autorisations en utilisant Gate
//        if (!Gate::allows('edit-bill', $bill)) {
//            abort(403, 'Action non autorisée.');
//        }

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
        $bill->load(['client', 'items.product']);

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

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $bill->getOriginal();

        // Mettre à jour la facture
        $bill->update([
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'tax_rate' => $validated['tax_rate'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? $bill->status,
        ]);

        // Enregistrer l'activité de mise à jour
        ActivityLogger::logUpdated($bill, $oldValues, "Facture {$bill->reference} modifiée par " . Auth::user()->name);

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

        // Enregistrer l'activité de suppression
        ActivityLogger::logDeleted($bill, "Facture {$bill->reference} supprimée par " . Auth::user()->name);

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

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $bill->getOriginal();

        // Mettre à jour le statut
        $bill->update(['status' => $newStatus]);

        // Enregistrer l'activité de changement de statut
        ActivityLogger::logUpdated($bill, $oldValues, "Statut de la facture {$bill->reference} changé de {$oldStatus} à {$newStatus} par " . Auth::user()->name);

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
        // Incrémenter le compteur de réimpressions
        $bill->reprint_count = ($bill->reprint_count ?? 0) + 1;
        $bill->save();

        // Récupérer les paramètres de l'entreprise
        $settings = Setting::first() ?? new \stdClass();

        // Charger les relations nécessaires
        $bill->load(['client', 'items.product', 'shop', 'seller']);

        // Conversion du chemin Storage en chemin réel pour l'accès du PDF
        if ($settings && $settings->logo_path) {
            $logoRealPath = storage_path('app/public/' . $settings->logo_path);
            $settings->logo_real_path = $logoRealPath;
        }

        // Générer le QR code
        try {
            $qrCode = $this->generateQrCode($bill);
            if (!$qrCode) {
                Log::warning('QR code non généré pour la facture ' . $bill->reference);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code pour PDF: ' . $e->getMessage());
            $qrCode = null;
        }

        $pdf = PDF::loadView('bills.pdf', compact('bill', 'settings', 'qrCode'));

        // Configurer dompdf pour permettre les images base64 et SVG
        $dompdf = $pdf->getDomPDF();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf->setOptions($options);

        return $pdf->download('facture_' . $bill->reference . '.pdf');
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
        // Incrémenter le compteur de réimpressions
        $bill->reprint_count = ($bill->reprint_count ?? 0) + 1;
        $bill->save();

        // Charger les relations nécessaires
        $bill->load(['client', 'items.product', 'shop', 'seller']);

        // Récupérer les paramètres de l'entreprise
        $settings = Setting::first() ?? new \stdClass();
        $company = $settings->company_name ?? config('app.name');
        $address = $settings->address ?? '';
        $phone = $settings->phone ?? '';

        // Générer le QR code
        try {
            $qrCode = $this->generateQrCode($bill);
            if (!$qrCode) {
                Log::warning('QR code non généré pour la facture ' . $bill->reference);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code pour impression: ' . $e->getMessage());
            $qrCode = null;
        }

        // Logo
        $logo = $settings->logo_path ? asset('storage/' . $settings->logo_path) : null;

        return view('bills.print', compact('bill', 'company', 'address', 'phone', 'qrCode', 'logo'));
    }

    /**
     * Ajouter une signature à la facture.
     */
    public function addSignature(Request $request, Bill $bill)
    {
        // Vérifier les autorisations en utilisant Gate
        if (!Gate::allows('edit-bill', $bill)) {
            abort(403, 'Action non autorisée.');
        }

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
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        $bills = $query->orderBy('date', 'desc')->paginate(15);

        $shops = Gate::allows('admin')
            ? Shop::all()
            : Auth::user()->shops;

        $sellers = User::where('role', 'vendeur')->get();

        // Si l'utilisateur est un vendeur, ne montrer que les clients de sa boutique
        if (Auth::user()->role === 'vendeur') {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $clients = Client::whereHas('bills', function ($query) use ($shopIds) {
                $query->whereIn('shop_id', $shopIds);
            })->get();
        } else {
            $clients = Client::all();
        }

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

        // Vérifier que la facture appartient à une boutique à laquelle l'utilisateur a accès
        if (!Gate::allows('admin')) {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            if (!in_array($bill->shop_id, $shopIds)) {
                abort(403, 'Vous n\'avez pas accès à cette facture.');
            }
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

    /**
     * Générer un QR code pour la facture.
     */
    public function generateQrCode(Bill $bill)
    {
        try {
            // Charger les relations nécessaires si elles ne sont pas déjà chargées
            if (!$bill->relationLoaded('client') || !$bill->relationLoaded('shop') || !$bill->relationLoaded('seller')) {
                $bill->load(['client', 'shop', 'seller']);
            }

            // Générer les données pour le QR code
            $data = json_encode([
                'reference' => $bill->reference,
                'date' => $bill->date->format('Y-m-d H:i:s'),
                'total' => $bill->total,
                'client' => $bill->client->name,
                'shop' => $bill->shop->name,
                'seller' => $bill->seller->name
            ]);

            // Options pour le QR code
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => true,
                'imageTransparent' => false,
            ]);

            // Générer le QR code
            $qrcode = new QRCode($options);
            $qrcode_base64 = $qrcode->render($data);

            // Retourner uniquement la partie base64 sans le préfixe
            return str_replace('data:image/png;base64,', '', $qrcode_base64);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }
}
