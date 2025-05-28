<?php

namespace App\Http\Controllers;

use App\Models\Barter;
use App\Models\BarterImage;
use App\Models\BarterItem;
use App\Models\BarterItemImage;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\TemporaryUpload;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BarterController extends Controller
{
    /**
     * Affiche la liste des trocs
     */
    /**
     * Affiche la liste des trocs
     */
    public function index(Request $request)
    {
        $query = Barter::with(['client', 'seller', 'shop', 'items', 'bill']);

        // Recherche textuelle
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seller', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('shop', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtres
        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->input('shop_id'));
        }

        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->input('seller_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtre par produit
        if ($request->has('product_id')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->input('product_id'));
            });
        }

        // Non-admins ne voient que les trocs de leurs boutiques
        if (!Gate::allows('admin')) {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        $barters = $query->orderBy('created_at', 'desc')->paginate(15);

        $clients = Client::orderBy('name')->get();
        $shops = Gate::allows('admin')
            ? Shop::orderBy('name')->get()
            : Auth::user()->shops;
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();

        // Optionnellement passer un produit si le filtre par produit est activé
        $product = null;
        if ($request->has('product_id')) {
            $product = Product::find($request->input('product_id'));
        }

        return view('barters.index', compact('barters', 'clients', 'shops', 'sellers', 'product'));
    }
    /**
     * Affiche le formulaire pour créer un nouveau troc
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->where('is_barterable', true)->where('type', 'physical')->where('stock_quantity', '>', 0)->get();
        $shops = Gate::allows('admin')
            ? Shop::orderBy('name')->get()
            : Auth::user()->shops;
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();

        return view('barters.create', compact('clients', 'products', 'shops', 'sellers'));
    }

    /**
     * Enregistre un nouveau troc
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shop_id' => 'required|exists:shops,id',
            'seller_id' => 'required|exists:users,id',
            'type' => 'required|in:same_type,different_type',
            'given_items' => 'required|array',
            'given_items.*.name' => 'required|string',
            'given_items.*.description' => 'nullable|string',
            'given_items.*.value' => 'required|numeric|min:0',
            'given_items.*.quantity' => 'required|integer|min:1',
            'received_items' => 'required|array',
            'received_items.*.name' => 'required|string',
            'received_items.*.description' => 'nullable|string',
            'received_items.*.value' => 'required|numeric|min:0',
            'received_items.*.quantity' => 'required|integer|min:1',
            'received_items.*.product_id' => 'nullable|exists:products,id',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'images.*' => 'nullable|image|max:5120', // Ajout de la validation pour les images
        ]);

        // Calculer les totaux
        $givenValue = 0;
        foreach ($validated['given_items'] as $item) {
            $givenValue += $item['value'] * $item['quantity'];
        }

        $receivedValue = 0;
        foreach ($validated['received_items'] as $item) {
            $receivedValue += $item['value'] * $item['quantity'];
        }

        $additionalPayment = $receivedValue - $givenValue;

        // Créer le troc
        $barter = Barter::create([
            'reference' => Barter::generateReference(),
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'user_id' => Auth::id(),
            'seller_id' => $validated['seller_id'],
            'type' => $validated['type'],
            'value_given' => $givenValue,
            'value_received' => $receivedValue,
            'additional_payment' => $additionalPayment,
            'payment_method' => $validated['payment_method'] ?? null,
            'notes' => $validated['description'] ?? null,
            // 'status' => 'pending',
        ]);

        // Ajouter les articles donnés
        foreach ($validated['given_items'] as $item) {
            BarterItem::create([
                'barter_id' => $barter->id,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'type' => 'given',
                'value' => $item['value'],
                'quantity' => $item['quantity'],
                // pas de product_id car ce sont des articles donnés par le client
            ]);
        }

        // Ajouter les articles reçus
        foreach ($validated['received_items'] as $item) {
            $barterItem = BarterItem::create([
                'barter_id' => $barter->id,
                'product_id' => $item['product_id'] ?? null,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'type' => 'received',
                'value' => $item['value'],
                'quantity' => $item['quantity'],
            ]);

            // Si cet article est lié à un produit, mettre à jour le stock
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->stock_quantity -= $item['quantity'];
                    $product->save();

                    // Enregistrer le mouvement d'inventaire
                    $product->inventoryMovements()->create([
                        'type' => 'troc',
                        'quantity' => -$item['quantity'],
                        'reference' => $barter->reference,
                        'user_id' => Auth::id(),
                        'unit_price' => $item['value'],
                        'total_price' => $item['value'] * $item['quantity'],
                        'stock_before' => $product->stock_quantity + $item['quantity'],
                        'stock_after' => $product->stock_quantity,
                        'shop_id' => $validated['shop_id'],
                    ]);
                }
            }
        }

        // Traitement des images téléchargées
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $image) {
                // Stocker l'image dans le stockage public
                $path = $image->store('barter-images', 'public');

                // Créer l'enregistrement d'image
                BarterImage::create([
                    'barter_id' => $barter->id,
                    'path' => $path,
                    'description' => $request->input('image_descriptions.'.$key, null),
                    'type' => $request->input('image_types.'.$key, 'given'), // Par défaut 'given'
                ]);
            }
        }

        // Générer automatiquement une facture pour le troc
        $bill = $barter->generateBill();

        // Enregistrer l'activité de création de troc
        ActivityLogger::logCreated($barter, "Troc {$barter->reference} créé par " . Auth::user()?->name);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc créé avec succès' . ($bill ? ' et facture générée automatiquement' : ''));
    }

    /**
     * Affiche les détails d'un troc
     */
    public function show(Barter $barter)
    {
        $barter->load(['client', 'seller', 'user', 'shop', 'items', 'images', 'bill']);

        return view('barters.show', compact('barter'));
    }

    /**
     * Affiche le formulaire pour modifier un troc
     */
    public function edit(Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible de modifier un troc qui n\'est pas en attente');
        }

        $barter->load(['client', 'seller', 'user', 'shop', 'items', 'images']);

        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $shops = Gate::allows('admin')
            ? Shop::orderBy('name')->get()
            : Auth::user()->shops;
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();

        return view('barters.edit', compact('barter', 'clients', 'products', 'shops', 'sellers'));
    }

    /**
     * Met à jour un troc
     */
    public function update(Request $request, Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible de modifier un troc qui n\'est pas en attente');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shop_id' => 'required|exists:shops,id',
            'seller_id' => 'required|exists:users,id',
            'type' => 'required|in:same_type,different_type',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $barter->getOriginal();

        $barter->update([
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'seller_id' => $validated['seller_id'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
        ]);

        // Enregistrer l'activité de mise à jour
        ActivityLogger::logUpdated($barter, $oldValues, "Troc {$barter->reference} modifié par " . Auth::user()?->name);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc mis à jour avec succès');
    }

    /**
     * Supprime un troc
     */
    public function destroy(Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible de supprimer un troc qui n\'est pas en attente');
        }

        // Restaurer le stock pour les produits liés
        foreach ($barter->items as $item) {
            if ($item->type === 'received' && $item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock_quantity += $item->quantity;
                    $product->save();

                    // Enregistrer le mouvement d'inventaire (annulation)
                    $product->inventoryMovements()->create([
                        'type' => 'annulation_troc',
                        'quantity' => $item->quantity,
                        'reference' => $barter->reference,
                        'user_id' => Auth::id(),
                        'unit_price' => $item->value,
                        'total_price' => $item->value * $item->quantity,
                        'stock_before' => $product->stock_quantity - $item->quantity,
                        'stock_after' => $product->stock_quantity,
                        'shop_id' => $barter->shop_id,
                    ]);
                }
            }
        }

        // Supprimer les images du troc
        foreach ($barter->images as $image) {
            Storage::delete($image->path);
        }

        // Supprimer la facture associée au troc s'il y en a une
        if ($barter->bill) {
            $barter->bill->delete();
        }

        $barter->delete();

        // Enregistrer l'activité de suppression
        ActivityLogger::logDeleted($barter, "Troc {$barter->reference} supprimé par " . Auth::user()?->name);

        return redirect()->route('barters.index')
            ->with('success', 'Troc supprimé avec succès');
    }

    /**
     * Marque un troc comme complété
     */
    public function complete(Request $request, Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible de compléter un troc qui n\'est pas en attente');
        }

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $barter->getOriginal();

        $barter->update([
            'status' => 'completed',
        ]);

        // Générer une facture si elle n'existe pas encore
        $bill = $barter->bill ?? $barter->generateBill();

        // Enregistrer l'activité de complétion
        ActivityLogger::logUpdated($barter, $oldValues, "Troc {$barter->reference} marqué comme complété par " . Auth::user()?->name);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc marqué comme complété' . ($bill ? ' et facture générée' : ''));
    }

    /**
     * Annule un troc
     */
    public function cancel(Request $request, Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible d\'annuler un troc qui n\'est pas en attente');
        }

        // Sauvegarder les valeurs originales pour l'historique
        $oldValues = $barter->getOriginal();

        // Restaurer le stock pour les produits liés
        foreach ($barter->items as $item) {
            if ($item->type === 'received' && $item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock_quantity += $item->quantity;
                    $product->save();

                    // Enregistrer le mouvement d'inventaire (annulation)
                    $product->inventoryMovements()->create([
                        'type' => 'annulation_troc',
                        'quantity' => $item->quantity,
                        'reference' => $barter->reference,
                        'user_id' => Auth::id(),
                        'unit_price' => $item->value,
                        'total_price' => $item->value * $item->quantity,
                        'stock_before' => $product->stock_quantity - $item->quantity,
                        'stock_after' => $product->stock_quantity,
                        'shop_id' => $barter->shop_id,
                    ]);
                }
            }
        }

        $barter->update([
            'status' => 'cancelled',
        ]);

        // Enregistrer l'activité d'annulation
        ActivityLogger::logUpdated($barter, $oldValues, "Troc {$barter->reference} annulé par " . Auth::user()?->name);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc annulé avec succès');
    }

    /**
     * Ajoute des images à un troc
     */
    public function addImages(Request $request, Barter $barter)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:5120', // 5MB max
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:255',
            'types' => 'required|array',
            'types.*' => 'required|in:given,received',
            'item_id' => 'nullable|exists:barter_items,id', // Paramètre facultatif pour associer l'image à un item spécifique
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $image) {
                // Déterminer si l'image doit être associée à un item spécifique ou au troc lui-même
                if ($request->has('item_id') && $request->input('item_id')) {
                    // Récupérer l'item de troc
                    $barterItem = BarterItem::findOrFail($request->input('item_id'));

                    // Vérifier que l'item appartient bien à ce troc
                    if ($barterItem->barter_id != $barter->id) {
                        return redirect()->route('barters.show', $barter)
                            ->with('error', 'L\'item spécifié n\'appartient pas à ce troc');
                    }

                    // Stocker l'image dans le stockage public
                    $path = $image->store('barter-items', 'public');

                    // Créer l'enregistrement d'image d'item
                    $barterItem->images()->create([
                        'path' => $path,
                        'description' => $validated['descriptions'][$key] ?? null,
                        'type' => $validated['types'][$key],
                        'order' => $key,
                    ]);
                } else {
                    // Stocker l'image pour le troc lui-même
                    $path = $image->store('barter-images', 'public');

                    // Créer l'enregistrement d'image de troc
                    BarterImage::create([
                        'barter_id' => $barter->id,
                        'path' => $path,
                        'description' => $validated['descriptions'][$key] ?? null,
                        'type' => $validated['types'][$key],
                    ]);
                }
            }
        }

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Images ajoutées avec succès');
    }

    /**
     * Supprime une image d'un troc
     */
    public function deleteImage(BarterImage $image)
    {
        $barterId = $image->barter_id;

        // Supprimer le fichier
        Storage::delete($image->path);

        // Supprimer l'enregistrement
        $image->delete();

        return redirect()->route('barters.show', $barterId)
            ->with('success', 'Image supprimée avec succès');
    }

    /**
     * Stocke les images pour un article de troc
     */
    private function storeBarterItemImages($request, $barterItem, $imageField = 'images')
    {
        if ($request->hasFile($imageField)) {
            $images = $request->file($imageField);
            foreach ($images as $key => $image) {
                $path = $image->store('barter-items', 'public');

                $barterItem->images()->create([
                    'path' => $path,
                    'description' => $request->input('image_descriptions.'.$key, null),
                    'order' => $key,
                ]);
            }
        }
    }

    /**
     * Traite un article dans un formulaire de troc
     */
    private function processBarterItem($barter, $item, $type)
    {
        // Créer l'article
        $barterItem = $barter->items()->create([
            'product_id' => $item['product_id'] ?? null,
            'name' => $item['name'] ?? 'Article sans nom',
            'description' => $item['description'] ?? null,
            'type' => $type,
            'value' => $item['value'] ?? 0,
            'quantity' => $item['quantity'] ?? 1
        ]);

        // Si des images sont spécifiées, les traiter
        if (isset($item['images'])) {
            // Cette partie dépend de la façon dont vous gérez les images
            // Si c'est un tableau d'IDs d'images déjà téléchargées, procédez comme suit :
            foreach ($item['images'] as $key => $imageId) {
                // Récupérer l'image temporaire et la déplacer
                $tempImage = TemporaryUpload::find($imageId);
                if ($tempImage) {
                    $barterItem->images()->create([
                        'path' => $tempImage->file_path,
                        'description' => $item['image_descriptions'][$key] ?? null,
                        'order' => $key,
                    ]);
                    $tempImage->delete(); // Supprimer l'entrée temporaire
                }
            }
        }

        return $barterItem;
    }

    /**
     * Affiche la liste des produits pouvant être utilisés pour le troc
     */
    public function getBarterableProducts(Request $request)
    {
        $products = Product::where('is_barterable', true)
            ->where('type', 'physical')
            ->where('stock_quantity', '>', 0)
            ->when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'default_price', 'stock_quantity')
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Ajouter des images à un article spécifique du troc
     */
    public function addItemImages(Request $request, $barterId, $itemId)
    {
        $barter = Barter::findOrFail($barterId);
        $barterItem = BarterItem::where('barter_id', $barterId)
            ->where('id', $itemId)
            ->firstOrFail();

        if (!$request->hasFile('images')) {
            return response()->json([
                'message' => 'Aucune image fournie'
            ], 400);
        }

        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'descriptions.*' => 'nullable|string|max:255',
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $key => $image) {
            $path = $image->store('barters/' . $barterId . '/items/' . $itemId, 'public');

            $description = $request->descriptions[$key] ?? null;

            $barterItemImage = BarterItemImage::create([
                'barter_item_id' => $barterItem->id,
                'path' => $path,
                'description' => $description,
            ]);

            $uploadedImages[] = [
                'id' => $barterItemImage->id,
                'url' => Storage::url($path),
                'description' => $barterItemImage->description,
            ];
        }

        return response()->json([
            'message' => 'Images ajoutées avec succès',
            'images' => $uploadedImages
        ]);
    }

    /**
     * Supprimer une image d'un article de troc
     */
    public function deleteItemImage($imageId)
    {
        $image = BarterItemImage::findOrFail($imageId);

        // Vérifie les autorisations si nécessaire

        $image->delete();

        return response()->json([
            'message' => 'Image supprimée avec succès'
        ]);
    }

    /**
     * Génère une facture pour un troc spécifique
     */
    public function generateBill(Barter $barter)
    {
        // Vérifier si une facture existe déjà
        if ($barter->bill) {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Une facture existe déjà pour ce troc');
        }

        // Générer la facture
        $bill = $barter->generateBill();

        if (!$bill) {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible de générer une facture pour ce troc');
        }

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Facture générée avec succès');
    }

    /**
     * Télécharge la facture d'un troc
     */
    public function downloadBill(Barter $barter)
    {
        // Vérifier si une facture existe déjà
        $bill = $barter->bill;

        if (!$bill) {
            // Générer une facture à la volée
            $bill = $barter->generateBill();

            if (!$bill) {
                return redirect()->route('barters.show', $barter)
                    ->with('error', 'Impossible de générer une facture pour ce troc');
            }
        }

        // Rediriger vers le téléchargement de la facture
        return redirect()->route('bills.download', $bill);
    }

    /**
     * Imprime la facture d'un troc
     */
    public function printBill(Barter $barter)
    {
        // Vérifier si une facture existe déjà
        $bill = $barter->bill;

        if (!$bill) {
            // Générer une facture à la volée
            $bill = $barter->generateBill();

            if (!$bill) {
                return redirect()->route('barters.show', $barter)
                    ->with('error', 'Impossible de générer une facture pour ce troc');
            }
        }

        // Rediriger vers l'impression de la facture
        return redirect()->route('bills.print', $bill);
    }

    /**
     * Affiche les statistiques des trocs
     */
    public function barterStats()
    {
        // Comptage des trocs par statut
        $statusCounts = Barter::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Valeur totale des trocs
        $totalGivenValue = Barter::sum('value_given');
        $totalReceivedValue = Barter::sum('value_received');

        // Produits les plus échangés
        $topProducts = BarterItem::selectRaw('product_id, sum(quantity) as total_quantity')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->with('product')
            ->get();

        // Trocs récents
        $recentBarters = Barter::with(['client', 'shop'])
            ->latest()
            ->limit(5)
            ->get();

        return view('barters.stats', compact(
            'statusCounts',
            'totalGivenValue',
            'totalReceivedValue',
            'topProducts',
            'recentBarters'
        ));
    }

    /**
     * Affiche les trocs filtrés par produit
     */
    public function indexByProduct(Request $request, Product $product)
    {
        $query = Barter::with(['client', 'seller', 'shop', 'items'])
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            });

        // Filtres existants
        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->input('shop_id'));
        }

        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->input('seller_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        // Non-admins ne voient que les trocs de leurs boutiques
        if (!Gate::allows('admin')) {
            $shopIds = Auth::user()->shops->pluck('id')->toArray();
            $query->whereIn('shop_id', $shopIds);
        }

        $barters = $query->orderBy('created_at', 'desc')->paginate(15);

        $clients = Client::orderBy('name')->get();
        $shops = Gate::allows('admin')
            ? Shop::orderBy('name')->get()
            : Auth::user()->shops;
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();

        // Passer le produit à la vue
        return view('barters.index', compact('barters', 'clients', 'shops', 'sellers', 'product'));
    }
}
