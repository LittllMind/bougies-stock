<?php

namespace App\Http\Controllers;

use App\Models\Bougie;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /**
     * Affiche la page catalogue avec Vue.js
     */
    public function index()
    {
        // Récupérer les bougies en stock pour Vue.js
        $bougies = Bougie::where('quantite', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Données pour Vue.js
        $bougiesJson = $bougies->map(function ($bougie) {
            return [
                'id' => $bougie->id,
                'reference' => $bougie->reference,
                'slug' => $bougie->slug,
                'nom' => $bougie->nom,
                'parfum' => $bougie->parfum,
                'collection' => $bougie->collection,
                'format' => $bougie->format,
                'prix' => $bougie->prix,
                'temps_brulure' => $bougie->temps_brulure,
                'notes' => $bougie->notes,
                'quantite' => $bougie->quantite,
                'image_url' => $bougie->image_url,
            ];
        });

        // Liste unique des parfums et collections pour les filtres
        $parfums = Bougie::where('quantite', '>', 0)
            ->distinct()
            ->pluck('parfum')
            ->filter()
            ->sort()
            ->values();

        $collections = Bougie::where('quantite', '>', 0)
            ->distinct()
            ->pluck('collection')
            ->filter()
            ->sort()
            ->values();

        return view('catalogue.index', [
            'bougies' => $bougiesJson->toArray(),
            'parfums' => $parfums->toArray(),
            'collections' => $collections->toArray(),
        ]);
    }

    /**
     * Affiche la page détail d'une bougie avec Vue.js
     * Accepte soit une référence (ex: BOUG-001), soit un ID numérique
     */
    public function show(string $reference)
    {
        // Détection si c'est un ID numérique ou une référence
        if (is_numeric($reference)) {
            $bougie = Bougie::find($reference);
        } else {
            $bougie = Bougie::where('reference', $reference)->first();
        }

        if (! $bougie) {
            abort(404);
        }

        // Vérifier que la bougie est en stock pour le catalogue
        if ($bougie->quantite <= 0) {
            abort(404);
        }

        return view('catalogue.show', [
            'bougie' => $bougie,
        ]);
    }
}
