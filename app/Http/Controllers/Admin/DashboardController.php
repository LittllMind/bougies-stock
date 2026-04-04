<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Bougie;
use App\Models\OrderItem;
use App\Models\StockAlert;
use App\Models\MouvementStock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord admin avec les statistiques globales
     */
    public function index()
    {
        // ===== NOUVELLES STATS DASHBOARD (T5.1) =====
        $today = now()->startOfDay();
        $thirtyDaysAgo = now()->subDays(30)->startOfDay();

        // Ventes du jour (commandes payées)
        $ventesDuJour = Order::where('statut', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total') ?? 0;

        // Stock faible (produits < 5 en stock)
        $stockFaible = Bougie::whereColumn('quantite', '<=', 'seuil_alerte')
            ->where('quantite', '>', 0)
            ->count();

        // Commandes en attente
        $commandesEnAttente = Order::where('statut', 'pending')->count();

        // Données pour graphique 30 jours
        $donneesVentes30Jours = collect(range(29, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->startOfDay();
            $nextDay = $date->copy()->addDay();
            
            return [
                'date' => $date->format('d/m'),
                'montant' => Order::where('statut', 'paid')
                    ->whereBetween('created_at', [$date, $nextDay])
                    ->sum('total') ?? 0,
            ];
        });

        // Dernières commandes (5 dernières)
        $dernieresCommandes = Order::with('user')
            ->whereIn('statut', ['paid', 'processing', 'ready'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Produits en stock critique (< 5)
        $produitsStockCritique = Bougie::whereColumn('quantite', '<=', 'seuil_alerte')
            ->where('quantite', '>', 0)
            ->orderBy('quantite', 'asc')
            ->take(5)
            ->get();

        // ===== STATISTIQUES BOUGIES (T4.1) - Conserver =====
        $totalBougies = Bougie::count();
        $bougiesEnStock = Bougie::where('quantite', '>', 0)->count();
        $valeurStockTotal = Bougie::query()
            ->selectRaw('SUM(quantite * prix) as valeur')
            ->value('valeur') ?? 0;
        $alertesActives = StockAlert::where('statut', 'actif')
            ->whereHas('stockable', function ($query) {
                $query->where('stockable_type', Bougie::class);
            })
            ->count();
        $derniersMouvements = MouvementStock::with('user')
            ->orderByDesc('date_mouvement')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            // T5.1 - Nouvelles stats
            'ventesDuJour',
            'stockFaible',
            'commandesEnAttente',
            'donneesVentes30Jours',
            'dernieresCommandes',
            'produitsStockCritique',
            // T4.1 - Stats existantes
            'totalBougies',
            'bougiesEnStock',
            'valeurStockTotal',
            'alertesActives',
            'derniersMouvements'
        ));
    }

    /**
     * Récupère les données pour la période sélectionnée
     */
    private function getDonneesPeriode(string $periode): array
    {
        $now = now();
        
        switch ($periode) {
            case 'semaine':
                $start = $now->copy()->startOfWeek();
                $points = collect(range(0, 6))->map(function ($day) use ($start) {
                    $date = $start->copy()->addDays($day);
                    return [
                        'label' => $date->format('D j'),
                        'ventes' => Order::where('statut', 'payee')
                            ->whereDate('created_at', $date)
                            ->sum('total') ?? 0,
                        'commandes' => Order::whereDate('created_at', $date)->count(),
                    ];
                });
                break;

            case 'mois':
                $start = $now->copy()->startOfMonth();
                $points = collect(range(0, $now->day - 1))->map(function ($day) use ($start) {
                    $date = $start->copy()->addDays($day);
                    return [
                        'label' => $date->format('j'),
                        'ventes' => Order::where('statut', 'payee')
                            ->whereDate('created_at', $date)
                            ->sum('total') ?? 0,
                        'commandes' => Order::whereDate('created_at', $date)->count(),
                    ];
                });
                break;

            case 'annee':
                $start = $now->copy()->startOfYear();
                $points = collect(range(1, $now->month))->map(function ($month) use ($now) {
                    $date = $now->copy()->month($month)->startOfMonth();
                    return [
                        'label' => $date->format('M'),
                        'ventes' => Order::where('statut', 'payee')
                            ->whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $month)
                            ->sum('total') ?? 0,
                        'commandes' => Order::whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $month)
                            ->count(),
                    ];
                });
                break;

            default:
                $points = collect([]);
        }

        return [
            'labels' => $points->pluck('label')->toArray(),
            'ventes' => $points->pluck('ventes')->toArray(),
            'commandes' => $points->pluck('commandes')->toArray(),
        ];
    }

    /**
     * API JSON pour les statistiques
     */
    public function statsApi()
    {
        $today = now()->startOfDay();
        $startOfMonth = now()->startOfMonth();

        return response()->json([
            'ventes_aujourdhui' => Order::where('statut', 'payee')->whereDate('created_at', $today)->sum('total') ?? 0,
            'commandes_aujourdhui' => Order::whereDate('created_at', $today)->count(),
            'ventes_mois' => Order::where('statut', 'payee')->whereBetween('created_at', [$startOfMonth, now()])->sum('total') ?? 0,
            'alertes_stock' => Bougie::whereColumn('quantite', '<=', 'seuil_alerte')->where('quantite', '>', 0)->count(),
            'ruptures_stock' => Bougie::where('quantite', '<=', 0)->count(),
            'valeur_stock' => Bougie::query()->selectRaw('SUM(quantite * prix) as valeur')->value('valeur') ?? 0,
        ]);
    }

    /**
     * API JSON pour les graphiques
     */
    public function chartsApi()
    {
        // Ventes sur les 12 derniers mois
        $ventes12Mois = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            
            return [
                'mois' => $date->format('Y-m'),
                'montant' => Order::where('statut', 'payee')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('total') ?? 0,
            ];
        });

        // Top produits ce mois
        $topProduitsMois = OrderItem::whereHas('order', fn($q) => $q->where('statut', 'payee')->whereMonth('created_at', now()->month))
            ->select('bougie_id', DB::raw('SUM(quantite) as total_vendu'))
            ->groupBy('bougie_id')
            ->orderByDesc('total_vendu')
            ->take(10)
            ->with('bougie:id,nom,reference')
            ->get();

        return response()->json([
            'ventes_12_mois' => $ventes12Mois,
            'top_produits_mois' => $topProduitsMois,
        ]);
    }
}
