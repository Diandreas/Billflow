<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    /**
     * Afficher la liste des téléphones
     */
    public function index()
    {
        $phones = Phone::with('clients')->paginate(10);
        return view('phones.index', compact('phones'));
    }

    /**
     * Afficher le formulaire de création de téléphone
     */
    public function create()
    {
        return view('phones.create');
    }

    /**
     * Enregistrer un nouveau téléphone
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|unique:phones|regex:/^[0-9+\s()-]+$/',
        ]);

        Phone::create($validated);

        return redirect()->route('phones.index')
            ->with('success', 'Numéro de téléphone ajouté avec succès.');
    }

    /**
     * Afficher un téléphone spécifique
     */
    public function show(Phone $phone)
    {
        return view('phones.show', compact('phone'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Phone $phone)
    {
        return view('phones.edit', compact('phone'));
    }

    /**
     * Mettre à jour un téléphone
     */
    public function update(Request $request, Phone $phone)
    {
        $validated = $request->validate([
            'number' => 'required|regex:/^[0-9+\s()-]+$/|unique:phones,number,' . $phone->id,
        ]);

        $phone->update($validated);

        return redirect()->route('phones.index')
            ->with('success', 'Numéro de téléphone mis à jour avec succès.');
    }

    /**
     * Supprimer un téléphone
     */
    public function destroy(Phone $phone)
    {
        $phone->delete();

        return redirect()->route('phones.index')
            ->with('success', 'Numéro de téléphone supprimé avec succès.');
    }
}
