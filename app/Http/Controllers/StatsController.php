<?php

namespace App\Http\Controllers;

use App\Models\Vinyle;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        // Nombre total de vinyles
        $totalVinyles = Vinyle::count();

        // Valeur totale du stock
        $valeurStock = Vinyle::selectRaw('SUM(prix * quantite) as total')
            ->value('total') ?? 0;

        // Nombre de vinyles en stock bas
        $stockBas = Vinyle::where('quantite', '<=', 5)->count();

        // Top 5 modèles (par nombre de vinyles)
        $topModeles = Vinyle::select('modele', DB::raw('COUNT(*) as count'))
            ->groupBy('modele')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Statistiques des ventes
        $totalVentes = Vente::count();
        $chiffreAffaires = Vente::sum('total');

        // Ventes par mois (3 derniers mois)
        $ventesParMois = Vente::selectRaw('DATE_FORMAT(date, "%Y-%m") as mois, SUM(total) as total')
            ->where('date', '>=', now()->subMonths(3))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Répartition par mode de paiement
        $paiements = Vente::select('mode_paiement', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('mode_paiement')
            ->get();

        return view('stats', compact(
            'totalVinyles',
            'valeurStock',
            'stockBas',
            'topModeles',
            'totalVentes',
            'chiffreAffaires',
            'ventesParMois',
            'paiements'
        ));
    }
}
