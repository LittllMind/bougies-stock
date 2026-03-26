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
        // Pour la vue catalogue.index, on a besoin de bougies simples sans pagination
        $bougies = Bougie::where('quantite', '>', 0)
            ->get(['id', 'reference', 'nom', 'parfum', 'collection', 'format', 'type_cire', 'prix', 'quantite'])
            ->toArray();

        // Parfums uniques pour le filtre
        $parfums = Bougie::distinct()->pluck('parfum')->filter()->values();
        
        // Collections uniques pour le filtre
        $collections = Bougie::distinct()->pluck('collection')->filter()->values();

        return view('catalogue.index', compact('bougies', 'parfums', 'collections'));
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
