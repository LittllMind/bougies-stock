<?php

namespace App\Http\Controllers;

use App\Models\Bougie;
use App\Models\Fond;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /**
     * Affiche le catalogue public des bougies (kiosque)
     */
    public function index(Request $request)
    {
        $query = Bougie::query();

        // Recherche par nom ou parfum
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('parfum', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Filtre par collection
        if ($request->filled('collection')) {
            $query->where('collection', $request->collection);
        }

        // Bougies avec pagination
        $bougies = $query->paginate(12)->withQueryString();

        // Collections uniques pour le filtre
        $collections = Bougie::distinct()->pluck('collection')->filter()->values();

        return view('kiosque', compact('bougies', 'collections'));
    }

    /**
     * Affiche le détail d'une bougie
     */
    public function show(string $reference)
    {
        $bougie = Bougie::where('reference', $reference)->firstOrFail();
        
        // Renvoyer 404 si la bougie n'est pas en stock
        if ($bougie->quantite <= 0) {
            abort(404);
        }
        
        // Bougies similaires (même collection)
        $similaires = Bougie::where('collection', $bougie->collection)
            ->where('id', '!=', $bougie->id)
            ->where('quantite', '>', 0)
            ->limit(4)
            ->get();

        return view('catalogue.show', compact('bougie', 'similaires'));
    }
}
