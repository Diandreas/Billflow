<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\VendorEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VendorEquipmentController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Les middlewares seront configurés dans les routes
    }

    /**
     * Display a listing of vendor equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = VendorEquipment::query()
            ->with(['user', 'shop', 'assignedBy', 'returnedTo']);

        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrage par magasin
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // Filtrage par vendeur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $equipment = $query->orderBy('id', 'desc')->paginate(10);
        $shops = Shop::orderBy('name')->get();
        $vendors = User::role('vendor')->orderBy('name')->get();

        return view('vendor-equipment.index', compact('equipment', 'shops', 'vendors'));
    }

    /**
     * Show the form for creating a new vendor equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shops = Shop::orderBy('name')->get();
        $vendors = User::role('vendor')->orderBy('name')->get();

        return view('vendor-equipment.create', compact('shops', 'vendors'));
    }

    /**
     * Store a newly created vendor equipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'serial_number' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'assigned_date' => 'required|date',
            'condition' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        // Ajout des informations supplémentaires
        $validated['status'] = 'assigned';
        $validated['assigned_by'] = Auth::id();

        VendorEquipment::create($validated);

        return redirect()->route('vendor-equipment.index')
            ->with('success', 'Équipement ajouté avec succès.');
    }

    /**
     * Display the specified vendor equipment.
     *
     * @param  \App\Models\VendorEquipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function show(VendorEquipment $equipment)
    {
        $equipment->load(['user', 'shop', 'assignedBy', 'returnedTo']);
        
        return view('vendor-equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified vendor equipment.
     *
     * @param  \App\Models\VendorEquipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorEquipment $equipment)
    {
        $shops = Shop::orderBy('name')->get();
        $vendors = User::role('vendor')->orderBy('name')->get();
        
        return view('vendor-equipment.edit', compact('equipment', 'shops', 'vendors'));
    }

    /**
     * Update the specified vendor equipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorEquipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorEquipment $equipment)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'serial_number' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'assigned_date' => 'required|date',
            'condition' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'status' => ['required', Rule::in(['assigned', 'returned'])],
        ]);

        // Si le statut est passé à "retourné", valider les informations de retour
        if ($request->status === 'returned' && $equipment->status === 'assigned') {
            $returnValidated = $request->validate([
                'returned_date' => 'required|date',
                'return_condition' => 'required|string|max:50',
                'return_notes' => 'nullable|string',
            ]);
            
            $validated = array_merge($validated, $returnValidated);
            $validated['returned_to'] = Auth::id();
        }

        $equipment->update($validated);

        return redirect()->route('vendor-equipment.index')
            ->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Show form to mark equipment as returned.
     *
     * @param  \App\Models\VendorEquipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function markReturned(VendorEquipment $equipment)
    {
        if ($equipment->status !== 'assigned') {
            return redirect()->route('vendor-equipment.show', $equipment)
                ->with('error', 'Cet équipement est déjà marqué comme retourné.');
        }
        
        return view('vendor-equipment.mark-returned', compact('equipment'));
    }

    /**
     * Process the return of equipment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorEquipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function markReturnedStore(Request $request, VendorEquipment $equipment)
    {
        if ($equipment->status !== 'assigned') {
            return redirect()->route('vendor-equipment.show', $equipment)
                ->with('error', 'Cet équipement est déjà marqué comme retourné.');
        }

        $validated = $request->validate([
            'returned_date' => 'required|date',
            'return_condition' => 'required|string|max:50',
            'return_notes' => 'nullable|string',
        ]);

        $equipment->update([
            'status' => 'returned',
            'returned_date' => $validated['returned_date'],
            'return_condition' => $validated['return_condition'],
            'return_notes' => $validated['return_notes'],
            'returned_to' => Auth::id(),
        ]);

        return redirect()->route('vendor-equipment.show', $equipment)
            ->with('success', 'Équipement marqué comme retourné avec succès.');
    }

    /**
     * Remove the specified vendor equipment from storage.
     *
     * @param  \App\Models\VendorEquipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorEquipment $equipment)
    {
        $equipment->delete();

        return redirect()->route('vendor-equipment.index')
            ->with('success', 'Équipement supprimé avec succès.');
    }

    /**
     * Affiche les équipements associés au vendeur connecté
     */
    public function myEquipment()
    {
        $equipments = VendorEquipment::where('user_id', Auth::id())
            ->with(['shop'])
            ->latest('assigned_date')
            ->paginate(15);
            
        return view('vendor-equipment.my-equipment', compact('equipments'));
    }
} 