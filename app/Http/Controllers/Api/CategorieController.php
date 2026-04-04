<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bougie;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour les catégories/collections
 * 
 * Endpoint: GET /api/categories
 */
class CategorieController extends Controller
{
    /**
     * Retourne la liste des collections avec nombre de bougies disponibles
     */
    public function index(): JsonResponse
    {
        // Récupérer toutes les collections distinctes des bougies en stock
        $collections = Bougie::where('quantite', '>', 0)
            ->whereNotNull('collection')
            ->distinct()
            ->pluck('collection');

        $result = [];
        foreach ($collections as $collection) {
            if (empty($collection)) {
                continue;
            }
            
            $count = Bougie::where('collection', $collection)
                ->where('quantite', '>', 0)
                ->count();
            
            $result[] = [
                'name' => $collection,
                'count' => $count,
            ];
        }

        // Tri par nom
        usort($result, fn($a, $b) => strcmp($a['name'], $b['name']));

        return response()->json([
            'data' => $result,
        ]);
    }
}
