<?php

namespace App\Http\Controllers;

use App\Models\Vinyle;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        // ---------- 1. Période choisie ----------
        $periode = $request->get('periode', '3m'); // 3 derniers mois par défaut

        switch ($periode) {
            case '30j':
                $startDate      = now()->subDays(30)->startOfDay();
                $sqlGroupFormat = '%Y-%m-%d';   // groupement par jour
                $grouping       = 'day';
                $periodeLabel   = '30 derniers jours';
                break;

            case '12m':
                $startDate      = now()->subMonthsNoOverflow(12)->startOfMonth();
                $sqlGroupFormat = '%Y-%m';      // groupement par mois
                $grouping       = 'month';
                $periodeLabel   = '12 derniers mois';
                break;

            case 'all':
                $startDate      = null;         // pas de filtre
                $sqlGroupFormat = '%Y-%m';      // groupement par mois
                $grouping       = 'month';
                $periodeLabel   = 'depuis le début';
                break;

            case '3m':
            default:
                // ⬅️ on passe aussi en groupement JOUR pour les 3 derniers mois
                $startDate      = now()->subMonthsNoOverflow(3)->startOfDay();
                $sqlGroupFormat = '%Y-%m-%d';   // groupement par jour
                $grouping       = 'day';
                $periode        = '3m';
                $periodeLabel   = '3 derniers mois';
                break;
        }

        // ---------- 2. Stats catalogue (indépendantes de la période) ----------
        $totalVinyles = Vinyle::count();

        $valeurStock = Vinyle::selectRaw('SUM(prix * quantite) as total')
            ->value('total') ?? 0;

        // Stock bas : 1 à 3
        $stockBas = Vinyle::where('quantite', '>', 0)
            ->where('quantite', '<=', 3)
            ->count();

        // Ruptures de stock : 0 ou moins
        $rupturesStock = Vinyle::where('quantite', '<=', 0)->count();

        // ---------- 3. Base de requête sur les ventes (filtrée par période) ----------
        $ventesQuery = Vente::query();

        if ($startDate) {
            $ventesQuery->where('date', '>=', $startDate);
        }

        // ---------- 4. Stats ventes sur la période ----------
        $totalVentes     = (clone $ventesQuery)->count();
        $chiffreAffaires = (clone $ventesQuery)->sum('total') ?? 0;

        // Ventes agrégées par jour ou par mois selon la période
        $ventesParPeriode = (clone $ventesQuery)
            ->selectRaw('DATE_FORMAT(date, ?) as periode, SUM(total) as total', [$sqlGroupFormat])
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        // Répartition par mode de paiement
        $paiements = (clone $ventesQuery)
            ->select('mode_paiement', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('mode_paiement')
            ->get();

        // ---------- 5. Vinyles vendus & top 10 modèles vendus ----------
        $venteIds = (clone $ventesQuery)->pluck('id');

        $nbVinylesVendus = 0;
        $topModelesVendus = collect();

        if ($venteIds->isNotEmpty()) {
            // Somme des quantités vendues sur la période
            $nbVinylesVendus = DB::table('ligne_ventes') // adapte le nom de table si besoin
                ->whereIn('vente_id', $venteIds)
                ->sum('quantite');

            // Top 10 modèles vendus (sur la période)
            $topModelesVendus = DB::table('ligne_ventes') // adapte le nom de table si besoin
                ->join('vinyles', 'ligne_ventes.vinyle_id', '=', 'vinyles.id')
                ->whereIn('ligne_ventes.vente_id', $venteIds)
                ->select('vinyles.nom', DB::raw('SUM(ligne_ventes.quantite) as total_vendus'))
                ->groupBy('vinyles.nom')
                ->orderByDesc('total_vendus')
                ->limit(15)
                ->get();
        }

        // ---------- 6. CA moyen par jour & panier moyen ----------
        // Nombre de jours couverts par la période
        if ($startDate) {
            $daysCount = max(1, $startDate->diffInDays(now()) + 1);
        } else {
            $range = Vente::selectRaw('MIN(date) as min_date, MAX(date) as max_date')->first();
            if ($range && $range->min_date && $range->max_date) {
                $min = \Carbon\Carbon::parse($range->min_date);
                $max = \Carbon\Carbon::parse($range->max_date);
                $daysCount = max(1, $min->diffInDays($max) + 1);
            } else {
                $daysCount = 1;
            }
        }

        $caMoyenParJour = $daysCount > 0 ? $chiffreAffaires / $daysCount : 0;
        $panierMoyen    = $totalVentes > 0 ? $chiffreAffaires / $totalVentes : 0;

        return view('stats', compact(
            'totalVinyles',
            'valeurStock',
            'stockBas',
            'rupturesStock',
            'totalVentes',
            'chiffreAffaires',
            'ventesParPeriode',
            'paiements',
            'periode',
            'periodeLabel',
            'grouping',
            'nbVinylesVendus',
            'caMoyenParJour',
            'panierMoyen',
            'topModelesVendus'
        ));
    }
}
