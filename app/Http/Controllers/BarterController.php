<?php

namespace App\Http\Controllers;

use App\Models\Barter;
use App\Models\BarterImage;
use App\Models\BarterItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\TemporaryUpload;
use App\Models\BarterItemImage;

class BarterController extends Controller
{
    public function index(Request $request)
    {
        $query = Barter::with(['client', 'seller', 'shop', 'items']);

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

        return view('barters.index', compact('barters', 'clients', 'shops', 'sellers'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $shops = Gate::allows('admin') 
            ? Shop::orderBy('name')->get() 
            : Auth::user()->shops;
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();

        return view('barters.create', compact('clients', 'products', 'shops', 'sellers'));
    }

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
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
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

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc créé avec succès');
    }

    public function show(Barter $barter)
    {
        $barter->load(['client', 'seller', 'user', 'shop', 'items', 'images']);

        return view('barters.show', compact('barter'));
    }

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

        $barter->update([
            'client_id' => $validated['client_id'],
            'shop_id' => $validated['shop_id'],
            'seller_id' => $validated['seller_id'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
        ]);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc mis à jour avec succès');
    }

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

        $barter->delete();

        return redirect()->route('barters.index')
            ->with('success', 'Troc supprimé avec succès');
    }

    public function complete(Request $request, Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible de compléter un troc qui n\'est pas en attente');
        }

        $barter->update([
            'status' => 'completed',
        ]);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc marqué comme complété');
    }

    public function cancel(Request $request, Barter $barter)
    {
        if ($barter->status !== 'pending') {
            return redirect()->route('barters.show', $barter)
                ->with('error', 'Impossible d\'annuler un troc qui n\'est pas en attente');
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

        $barter->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('barters.show', $barter)
            ->with('success', 'Troc annulé avec succès');
    }

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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $barterId
     * @param  int  $itemId
     * @return \Illuminate\Http\JsonResponse
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
            $path = $image->store('public/barters/' . $barterId . '/items/' . $itemId);
            
            $description = $request->descriptions[$key] ?? null;
            
            $barterItemImage = BarterItemImage::create([
                'barter_item_id' => $barterItem->id,
                'path' => $path,
                'description' => $description,
            ]);
            
            $uploadedImages[] = [
                'id' => $barterItemImage->id,
                'url' => $barterItemImage->url,
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
     *
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
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
} 