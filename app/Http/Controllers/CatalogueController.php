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
        $query = Bougie::query()
            ->where('quantite', '>', 0)
            ->where('prix', '>', 0);

        // Recherche par mot-clé
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('parfum', 'like', "%{$search}%")
                  ->orWhere('collection', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Filtre par collection
        if ($collection = $request->get('collection')) {
            $query->where('collection', $collection);
        }

        // Filtre par type de cire
        if ($typeCire = $request->get('type_cire')) {
            $query->where('type_cire', $typeCire);
        }

        // Tri
        $sort = $request->get('sort', 'nom');
        $order = $request->get('order', 'asc');
        
        $allowedSorts = ['nom', 'prix', 'collection', 'quantite'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'nom';
        }
        
        $query->orderBy($sort, $order);

        $bougies = $query->paginate(12)->withQueryString();

        // Collections uniques pour le filtre
        $collections = Bougie::distinct()->pluck('collection')->filter()->values();
        
        // Types de cire uniques
        $typesCire = Bougie::distinct()->pluck('type_cire')->filter()->values();

        return view('kiosque', compact('bougies', 'collections', 'typesCire', 'search', 'sort', 'order'));
    }

    /**
     * Affiche le détail d'une bougie
     */
    public function show(string $reference)
    {
        $bougie = Bougie::where('reference', $reference)->firstOrFail();
        
        // Bougies similaires (même collection)
        $similaires = Bougie::where('collection', $bougie->collection)
            ->where('id', '!=', $bougie->id)
            ->where('quantite', '>', 0)
            ->limit(4)
            ->get();

        // Fonds disponibles si applicable
        $fonds = Fond::where('quantite', '>', 0)
            ->orderBy('nom')
            ->get();

        return view('catalogue.show', compact('bougie', 'similaires', 'fonds'));
    }
}
