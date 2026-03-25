<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Bougie;
use App\Models\Fond;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord admin avec les statistiques globales
     */
    public function index()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Statistiques des ventes (mois en cours)
        $ventesMois = Order::where('statut', 'livree')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total') ?? 0;

        // Nombre de commandes en cours (en_attente, en_preparation, prete)
        $commandesEnCours = Order::whereIn('statut', ['en_attente', 'en_preparation', 'prete'])
            ->count();

        // Valeur du stock bougies (quantite * prix de vente)
        $valeurStockBougies = Bougie::query()
            ->selectRaw('SUM(quantite * prix) as valeur')
            ->value('valeur') ?? 0;

        // Valeur du stock fonds (quantite * prix_vente)
        $valeurStockFonds = Fond::query()
            ->selectRaw('SUM(quantite * prix_vente) as valeur')
            ->value('valeur') ?? 0;

        // Total unites en stock
        $totalBougies = Bougie::sum('quantite') ?? 0;
        $totalFonds = Fond::sum('quantite') ?? 0;

        // Alertes stock faible (bougies avec quantite entre 1 et seuil_alerte)
        $alertesBougies = Bougie::whereColumn('quantite', '<=', 'seuil_alerte')
            ->where('quantite', '>', 0)
            ->count();

        // Ruptures de stock
        $rupturesBougies = Bougie::where('quantite', '<=', 0)->count();
        $rupturesFonds = Fond::where('quantite', '<=', 0)->count();

        // Dernieres commandes
        $dernieresCommandes = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Ventes des 6 derniers mois (pour graphique) - compatible SQLite
        $ventesMensuelles = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            return [
                'mois' => $date->format('M Y'),
                'montant' => Order::where('statut', 'livree')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('total') ?? 0,
            ];
        });

        return view('admin.dashboard', compact(
            'ventesMois',
            'commandesEnCours',
            'valeurStockBougies',
            'valeurStockFonds',
            'totalBougies',
            'totalFonds',
            'alertesBougies',
            'rupturesBougies',
            'rupturesFonds',
            'dernieresCommandes',
            'ventesMensuelles'
        ));
    }

    /**
     * API JSON pour les statistiques (utilisee par les graphiques)
     */
    public function statsApi()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return response()->json([
            'ventes_mois' => Order::where('statut', 'livree')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total') ?? 0,
            'commandes_en_cours' => Order::whereIn('statut', ['en_attente', 'en_preparation', 'prete'])->count(),
            'valeur_stock_bougies' => Bougie::query()->selectRaw('SUM(quantite * prix) as valeur')->value('valeur') ?? 0,
            'valeur_stock_fonds' => Fond::query()->selectRaw('SUM(quantite * prix_vente) as valeur')->value('valeur') ?? 0,
            'total_bougies' => Bougie::sum('quantite') ?? 0,
            'total_fonds' => Fond::sum('quantite') ?? 0,
            'alertes_stock' => Bougie::whereColumn('quantite', '<=', 'seuil_alerte')
                ->where('quantite', '>', 0)
                ->count(),
        ]);
    }

    /**
     * API JSON pour les graphiques temporels (ventes 12 mois, evolution stock)
     */
    public function chartsApi()
    {
        // Ventes sur les 12 derniers mois (exclut commandes annulees)
        $ventes12Mois = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            
            return [
                'mois' => $date->format('Y-m'),
                'montant' => Order::where('statut', 'livree')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('total') ?? 0,
            ];
        });

        // Evolution du stock bougies (12 derniers mois)
        // Simplifie: stock actuel seulement (pas d'historique des ventes detaille)
        $evolutionStockBougies = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            
            // Stock actuel uniquement (approximation simplifiee)
            $stockActuel = Bougie::sum('quantite') ?? 0;
            
            return [
                'mois' => $date->format('Y-m'),
                'quantite' => $stockActuel,
            ];
        });

        // Evolution du stock fonds (12 derniers mois)
        $evolutionStockFonds = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            
            // Stock actuel uniquement (approximation simplifiee)
            $stockFondsActuel = Fond::sum('quantite') ?? 0;
            
            return [
                'mois' => $date->format('Y-m'),
                'quantite' => $stockFondsActuel,
            ];
        });

        return response()->json([
            'ventes_12_mois' => $ventes12Mois,
            'evolution_stock_bougies' => $evolutionStockBougies,
            'evolution_stock_fonds' => $evolutionStockFonds,
        ]);
    }
}
