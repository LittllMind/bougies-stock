<?php

namespace App\Http\Controllers;

use App\Models\Bougie;
use Illuminate\Http\Request;

class CatalogueApiController extends Controller
{
    /**
     * Retourne la liste paginée des bougies disponibles (API JSON)
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
                  ->orWhere('parfum', 'like', "%{$search}%");
            });
        }

        // Filtre par collection
        if ($collection = $request->get('collection')) {
            $query->where('collection', $collection);
        }

        // Filtre par parfum
        if ($parfum = $request->get('parfum')) {
            $query->where('parfum', 'like', "%{$parfum}%");
        }

        // Filtre par prix maximum
        if ($prixMax = $request->get('prix_max')) {
            $query->where('prix', '<=', $prixMax);
        }

        // Tri
        $sort = $request->get('sort', 'nom');
        $order = $request->get('order', 'asc');
        
        $allowedSorts = ['nom', 'prix', 'collection', 'created_at', 'reference'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'nom';
        }
        
        $query->orderBy($sort, $order);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $bougies = $query->paginate($perPage);

        return response()->json($bougies);
    }

    /**
     * Retourne le détail d'une bougie spécifique (API JSON)
     */
    public function show(string $reference)
    {
        $bougie = Bougie::where('reference', $reference)
            ->where('quantite', '>', 0)
            ->first();

        if (!$bougie) {
            return response()->json(['message' => 'Bougie non trouvée'], 404);
        }

        return response()->json($bougie);
    }
}