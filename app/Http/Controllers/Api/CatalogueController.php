<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bougie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /**
     * Retourne la liste des bougies pour le catalogue public
     */
    public function index(Request $request): JsonResponse
    {
        $query = Bougie::query()
            ->where('quantite', '>', 0); // Uniquement en stock

        // Filtre par collection
        if ($request->has('collection')) {
            $query->where('collection', $request->input('collection'));
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
        $query->orderBy($sortField, $sortOrder);

        $bougies = $query->get();

        return response()->json([
            'data' => $bougies->map(function ($bougie) {
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
                    'stock_status' => $bougie->quantite > 0 ? 'en_stock' : 'epuise',
                ];
            }),
            'prev_page_url' => null,
            'next_page_url' => null,
        ]);
    }

    /**
     * Retourne le détail d'une bougie par sa référence
     */
    public function show(string $reference): JsonResponse
    {
        $bougie = Bougie::where('reference', $reference)->first();

        if (! $bougie) {
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
            ],
        ]);
    }

    /**
     * Retourne le détail d'une bougie par ID
     */
    public function detail(Bougie $bougie): JsonResponse
    {
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
            ],
        ]);
    }

    /**
     * Retourne les bougies similaires (même parfum) par ID
     */
    public function similaires(Bougie $bougie): JsonResponse
    {
        $similaires = Bougie::query()
            ->where('id', '!=', $bougie->id)
            ->where('parfum', $bougie->parfum)
            ->where('quantite', '>', 0)
            ->take(4)
            ->get();

        return response()->json([
            'data' => $similaires->map(function ($b) {
                return [
                    'id' => $b->id,
                    'reference' => $b->reference,
                    'nom' => $b->nom,
                    'parfum' => $b->parfum,
                    'collection' => $b->collection,
                    'format' => $b->format,
                    'prix' => $b->prix,
                    'quantite' => $b->quantite,
                ];
            }),
        ]);
    }
}
