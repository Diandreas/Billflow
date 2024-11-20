<?php

// app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Phone;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'nullable|in:M,F,Other',
            'birth' => 'nullable|date',
            'phones' => 'nullable|array',
            'phones.*' => 'required|string'
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'sex' => $validated['sex'],
            'birth' => $validated['birth'],
        ]);

        if (!empty($validated['phones'])) {
            foreach ($validated['phones'] as $phoneNumber) {
                $phone = Phone::firstOrCreate(['number' => $phoneNumber]);
                $client->phones()->attach($phone->id);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client->load('phones'),
                'message' => 'Client créé avec succès'
            ]);
        }

        return redirect()->back()->with('success', 'Client créé avec succès');
    }

    // API endpoint pour la recherche de clients
    public function search(Request $request)
    {
        $query = $request->get('q');
        $clients = Client::where('name', 'like', "%{$query}%")
            ->with('phones')
            ->take(5)
            ->get();
        return response()->json($clients);
    }
}
