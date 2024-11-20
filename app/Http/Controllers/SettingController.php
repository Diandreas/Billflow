<?php

// app/Http/Controllers/SettingController.php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

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
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $settings->fill($validated)->save();

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès');
    }
}
