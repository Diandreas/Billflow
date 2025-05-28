<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    /**
     * Affiche la liste des activités.
     */
    public function index(Request $request)
    {
        // Vérifie si l'utilisateur est administrateur
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }

        $query = Activity::with('user');

        // Filtrer par utilisateur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filtrer par action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filtrer par type de modèle
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->input('model_type'));
        }

        // Filtrer par date
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        // Recherche par description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', "%{$search}%");
        }

        // Trier par date (le plus récent d'abord)
        $activities = $query->latest()->paginate(50);

        // Obtenir la liste des utilisateurs pour le filtre
        $users = User::orderBy('name')->get();

        // Obtenir la liste des actions uniques pour le filtre
        $actions = Activity::distinct()->pluck('action')->filter()->sort()->values();

        // Obtenir la liste des types de modèles uniques pour le filtre
        $modelTypes = Activity::distinct()->pluck('model_type')->filter()->sort()->values();

        return view('activities.index', compact(
            'activities',
            'users',
            'actions',
            'modelTypes'
        ));
    }

    /**
     * Affiche les détails d'une activité spécifique.
     */
    public function show(Activity $activity)
    {
        // Vérifie si l'utilisateur est administrateur
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }

        return view('activities.show', compact('activity'));
    }

    /**
     * Exporte les activités au format CSV.
     */
    public function export(Request $request)
    {
        // Vérifie si l'utilisateur est administrateur
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }

        $query = Activity::with('user');

        // Appliquer les mêmes filtres que pour l'index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->input('model_type'));
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', "%{$search}%");
        }

        $activities = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=activities-export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Utilisateur',
                'Action',
                'Type de modèle',
                'ID du modèle',
                'Description',
                'Adresse IP',
                'Appareil',
                'Date'
            ]);

            // Données
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->user ? $activity->user->name : 'Système',
                    $activity->action,
                    $activity->model_type,
                    $activity->model_id,
                    $activity->description,
                    $activity->ip_address,
                    $activity->device,
                    $activity->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
