<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MouvementStock;
use App\Models\Bougie;
use Illuminate\Http\Request;

class MouvementStockController extends Controller
{
    /**
     * Afficher l'historique des mouvements de stock (spécifique bougies)
     */
    public function index(Request $request)
    {
        // Requête de base : uniquement les mouvements de bougies
        $query = MouvementStock::with('user')
            ->where('produit_type', 'bougie')
            ->orderBy('date_mouvement', 'desc');

        // Filtre par type (entrée/sortie)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par bougie spécifique
        if ($request->filled('bougie_id')) {
            $query->where('produit_id', $request->bougie_id);
        }

        // Filtre par date début
        if ($request->filled('date_debut')) {
            $query->where('date_mouvement', '>=', $request->date_debut . ' 00:00:00');
        }

        // Filtre par date fin
        if ($request->filled('date_fin')) {
            $query->where('date_mouvement', '<=', $request->date_fin . ' 23:59:59');
        }

        // Filtre par référence
        if ($request->filled('search')) {
            $query->where('reference', 'like', '%' . $request->search . '%');
        }

        $mouvements = $query->paginate(25)->withQueryString();

        // Statistics pour le dashboard
        $stats = [
            'total_entrees' => MouvementStock::entrees()
                ->where('produit_type', 'bougie')
                ->sum('quantite') ?: 0,
            'total_sorties' => MouvementStock::sorties()
                ->where('produit_type', 'bougie')
                ->sum('quantite') ?: 0,
            'aujourdhui' => MouvementStock::where('produit_type', 'bougie')
                ->whereDate('date_mouvement', today())
                ->count(),
        ];

        // Options pour les filtres
        $types = ['entree' => 'Entrées', 'sortie' => 'Sorties'];
        $bougies = Bougie::orderBy('nom')->select('id', 'nom', 'reference')->get();

        return view('admin.mouvements.index', compact(
            'mouvements',
            'stats',
            'types',
            'bougies'
        ));
    }
}