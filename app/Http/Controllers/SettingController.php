<?php

// app/Http/Controllers/SettingController.php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getSettings();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'siret' => 'nullable|string',
            'logo' => 'nullable|image|max:1024'
        ]);

        $settings = Setting::getSettings();

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            
            // Stocker le nouveau logo
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $settings->fill($validated)->save();

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès');
    }
}
