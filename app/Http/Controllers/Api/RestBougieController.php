<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bougie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur API REST pour les bougies
 * 
 * Endpoints:
 * - GET /api/bougies (liste avec filtres et pagination)
 * - GET /api/bougies/{id} (détail par ID)
 * - GET /api/bougies/{reference} (détail par référence)
 */
class RestBougieController extends Controller
{
    /**
     * Retourne la liste paginée des bougies avec filtres
     * 
     * Filtres supportés:
     * - collection: filtre par collection
     * - prix_min, prix_max: filtre par fourchette de prix
     * - search: recherche dans nom et parfum
     * - sort: champ de tri (nom, prix, quantite)
     * - order: ordre du tri (asc, desc)
     * - per_page: nombre d'items par page (défaut: 15, max: 100)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Bougie::query()
            ->where('quantite', '>', 0); // Uniquement en stock

        // Filtre par collection
        if ($request->has('collection')) {
            $query->where('collection', $request->input('collection'));
        }

        // Filtre par prix minimum
        if ($request->has('prix_min')) {
            $query->where('prix', '>=', $request->input('prix_min'));
        }

        // Filtre par prix maximum
        if ($request->has('prix_max')) {
            $query->where('prix', '<=', $request->input('prix_max'));
        }

        // Recherche par nom ou parfum
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('parfum', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortField = $request->input('sort', 'nom');
        $sortOrder = $request->input('order', 'asc');
        
        // Whitelist des champs de tri pour sécurité
        $allowedSortFields = ['nom', 'prix', 'quantite', 'reference', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nom';
        }
        
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = min((int) $request->input('per_page', 15), 100);
        $bougies = $query->paginate($perPage);

        return response()->json([
            'data' => collect($bougies->items())->map(function ($bougie) {
                return [
                    'id' => $bougie->id,
                    'reference' => $bougie->reference,
                    'nom' => $bougie->nom,
                    'parfum' => $bougie->parfum,
                    'collection' => $bougie->collection,
                    'format' => $bougie->format,
                    'type_cire' => $bougie->type_cire,
                    'prix' => $bougie->prix,
                    'quantite' => $bougie->quantite,
                    'stock_status' => $bougie->stock_status,
                ];
            })->values(),
            'meta' => [
                'total' => $bougies->total(),
                'per_page' => $bougies->perPage(),
                'current_page' => $bougies->currentPage(),
                'last_page' => $bougies->lastPage(),
            ],
        ]);
    }

    /**
     * Retourne le détail d'une bougie par ID ou référence
     */
    public function show(string $identifier): JsonResponse
    {
        // Déterminer si c'est un ID numérique ou une référence
        if (is_numeric($identifier)) {
            $bougie = Bougie::find($identifier);
        } else {
            $bougie = Bougie::where('reference', $identifier)->first();
        }

        if (!$bougie) {
            return response()->json([
                'message' => 'Bougie non trouvée',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $bougie->id,
                'reference' => $bougie->reference,
                'nom' => $bougie->nom,
                'parfum' => $bougie->parfum,
                'collection' => $bougie->collection,
                'format' => $bougie->format,
                'type_cire' => $bougie->type_cire,
                'temps_brulure' => $bougie->temps_brulure,
                'notes' => $bougie->notes,
                'prix' => $bougie->prix,
                'quantite' => $bougie->quantite,
                'stock_status' => $bougie->stock_status,
            ],
        ]);
    }
}
