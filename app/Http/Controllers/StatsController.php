<?php

namespace App\Http\Controllers;

use App\Models\Vinyle;
use App\Models\Vente;
use App\Models\LigneVente;
use App\Models\Fond;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        // ======================================================
        // 1. PÉRIODE CHOISIE
        // ======================================================
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
                $startDate      = now()->subMonthsNoOverflow(3)->startOfDay();
                $sqlGroupFormat = '%Y-%m-%d';   // groupement par jour
                $grouping       = 'day';
                $periode        = '3m';
                $periodeLabel   = '3 derniers mois';
                break;
        }

        // ======================================================
        // 2. COÛTS UNITAIRES
        // ======================================================
        $coutAchatVinyle = 8.50;
        $coutAchatFond   = 3.00;

        // ======================================================
        // 3. STATS CATALOGUE & STOCK (indépendantes de la période)
        // ======================================================

        // --- VINYLES ---

        // Nombre de modèles au catalogue
        $totalVinyles = Vinyle::count();

        // Quantité totale de vinyles en stock
        $totalQuantiteVinyles = Vinyle::sum('quantite') ?? 0;
        $quantiteVinylesStock = $totalQuantiteVinyles;

        // Valeur d'achat du stock vinyles
        $valeurStockAchatVinyles = $quantiteVinylesStock * $coutAchatVinyle;

        // Valeur du stock au prix catalogue (colonne "prix")
        $valeurStock = Vinyle::sum(DB::raw('prix * quantite')) ?? 0;

        // CA total historique (toutes ventes)
        $chiffreAffairesTotal = Vente::sum('total') ?? 0;

        // CA potentiel du stock vinyles
        $caStockPotentielVinyles = $valeurStock;

        // CA total possible (historique + stock)
        $caTotalPossibleVinyles = $chiffreAffairesTotal + $caStockPotentielVinyles;

        // --- FONDS (stock actuel) ---

        $stockMiroir = Fond::where('type', 'miroir')->sum('quantite') ?? 0;
        $stockDore   = Fond::where('type', 'dore')->sum('quantite') ?? 0;

        $quantiteFondsMiroirStock = $stockMiroir;
        $quantiteFondsDoreStock   = $stockDore;
        $quantiteFondsStockTotal  = $quantiteFondsMiroirStock + $quantiteFondsDoreStock;

        // Coût d'achat des fonds en stock
        $valeurStockFonds = $quantiteFondsStockTotal * $coutAchatFond;

        // ======================================================
        // 4. VINYLES – HISTORIQUE (toutes périodes)
        // ======================================================

        // Quantité totale de vinyles vendus
        $quantiteVinylesVendus = LigneVente::sum('quantite') ?? 0;

        // Quantité totale de vinyles achetés = stock + vendus
        $quantiteVinylesAchetes = $quantiteVinylesStock + $quantiteVinylesVendus;

        // Coût d'achat historique des vinyles vendus
        $coutAchatVinylesVendus = $quantiteVinylesVendus * $coutAchatVinyle;

        // Investissement total vinyles (stock + vendus)
        $investissementTotalVinyles = $quantiteVinylesAchetes * $coutAchatVinyle;

        // ======================================================
        // 5. FONDS – HISTORIQUE (toutes périodes, via LIGNE_VENTES)
        // ======================================================

        // Fonds vendus (toutes périodes) par type
        $quantiteFondsMiroirVendus = LigneVente::where('fond', 'miroir')->sum('quantite');
        $quantiteFondsDoreVendus   = LigneVente::where('fond', 'dore')->sum('quantite');

        $quantiteFondsVendusTotal  = $quantiteFondsMiroirVendus + $quantiteFondsDoreVendus;

        // Quantités achetées = stock restant + vendus
        $quantiteFondsMiroirAchetes = $quantiteFondsMiroirStock + $quantiteFondsMiroirVendus;
        $quantiteFondsDoreAchetes   = $quantiteFondsDoreStock   + $quantiteFondsDoreVendus;

        $quantiteFondsAchetesTotal  = $quantiteFondsMiroirAchetes + $quantiteFondsDoreAchetes;

        // Coût d'achat historique des fonds vendus
        $coutAchatFondsVendus = $quantiteFondsVendusTotal * $coutAchatFond;

        // Investissement total fonds (stock + vendus)
        $investissementTotalFonds = $quantiteFondsAchetesTotal * $coutAchatFond;

        // ======================================================
        // 6. MARGES GLOBALES (toutes périodes)
        // ======================================================

        $coutTotalHistorique    = $coutAchatVinylesVendus + $coutAchatFondsVendus;
        $margeBruteTotale       = $chiffreAffairesTotal - $coutTotalHistorique;
        $tauxMargeBruteTotale   = $chiffreAffairesTotal > 0
            ? ($margeBruteTotale / $chiffreAffairesTotal) * 100
            : 0;

        $margeMoyenneParVinyle  = $quantiteVinylesVendus > 0
            ? $margeBruteTotale / $quantiteVinylesVendus
            : 0;

        // Marge potentielle sur le stock vinyles (on ignore les fonds pour simplifier)
        $margePotentielleStock = $valeurStock - $valeurStockAchatVinyles;

        // ======================================================
        // 7. STATS VENTES SUR LA PÉRIODE
        // ======================================================

        // Ventes de la période
        $ventesPeriodeQuery = Vente::query();
        if ($startDate) {
            $ventesPeriodeQuery->where('created_at', '>=', $startDate);
        }
        $ventesPeriode = $ventesPeriodeQuery->get();

        $totalVentes     = $ventesPeriode->count();
        $chiffreAffaires = $ventesPeriode->sum('total');

        // CA moyen par jour sur la période
        if ($startDate) {
            $dateDebut = $startDate;
        } else {
            $minCreated = Vente::min('created_at');
            $dateDebut  = $minCreated ? \Carbon\Carbon::parse($minCreated) : null;
        }

        if ($dateDebut) {
            $nbJours = now()->diffInDays($dateDebut) + 1;
            $caMoyenParJour = $nbJours > 0 ? $chiffreAffaires / $nbJours : 0;
        } else {
            $caMoyenParJour = 0;
        }

        // Panier moyen
        $panierMoyen = $totalVentes > 0 ? $chiffreAffaires / $totalVentes : 0;

        // Vinyles vendus sur la période (quantité)
        $nbVinylesVendus = LigneVente::whereHas('vente', function ($q) use ($startDate) {
            if ($startDate) {
                $q->where('created_at', '>=', $startDate);
            }
        })
            ->sum('quantite') ?? 0;

        $coutVinylesVendusPeriode = $nbVinylesVendus * $coutAchatVinyle;

        // Fonds vendus sur la période
        $nbMiroirsVendusPeriode = LigneVente::where('fond', 'miroir')
            ->whereHas('vente', function ($q) use ($startDate) {
                if ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }
            })
            ->sum('quantite');

        $nbDoresVendusPeriode = LigneVente::where('fond', 'dore')
            ->whereHas('vente', function ($q) use ($startDate) {
                if ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }
            })
            ->sum('quantite');

        $coutFondsVendusPeriode = ($nbMiroirsVendusPeriode + $nbDoresVendusPeriode) * $coutAchatFond;

        // Marge brute sur la période
        $margeBrute = $chiffreAffaires - ($coutVinylesVendusPeriode + $coutFondsVendusPeriode);

        // ======================================================
        // 8. AGRÉGATIONS POUR GRAPHIQUES
        // ======================================================

        // Ventes groupées (jour ou mois)
        $ventesParPeriode = DB::table('ventes')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$sqlGroupFormat}') as periode"),
                DB::raw('SUM(total) as ca')
            )
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        // Répartition par mode de paiement
        $paiements = DB::table('ventes')
            ->select(
                'mode_paiement',
                DB::raw('COUNT(*) as nb_ventes'),
                DB::raw('SUM(total) as total')
            )
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })
            ->groupBy('mode_paiement')
            ->get();

        // Top modèles vendus sur la période
        $topModelesVendus = LigneVente::select(
            'vinyles.nom',
            DB::raw('SUM(ligne_ventes.quantite) as total_vendus')
        )
            ->join('vinyles', 'vinyles.id', '=', 'ligne_ventes.vinyle_id')
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereHas('vente', function ($sub) use ($startDate) {
                    $sub->where('created_at', '>=', $startDate);
                });
            })
            ->groupBy('vinyles.id', 'vinyles.nom')
            ->orderByDesc('total_vendus')
            ->limit(30)
            ->get();

        // Stock bas / ruptures
        $stockBas = Vinyle::where('quantite', '>', 0)
            ->where('quantite', '<=', 3)
            ->count();

        $rupturesStock = Vinyle::where('quantite', '<=', 0)->count();

        // ======================================================
        // 9. ENVOI À LA VUE
        // ======================================================

        return view('stats', [
            // Stock & catalogue
            'valeurStock'             => $valeurStock,
            'totalVinyles'            => $totalVinyles,
            'totalQuantiteVinyles'    => $totalQuantiteVinyles,
            'stockBas'                => $stockBas,
            'rupturesStock'           => $rupturesStock,

            // Période
            'totalVentes'             => $totalVentes,
            'chiffreAffaires'         => $chiffreAffaires,
            'ventesParPeriode'        => $ventesParPeriode,
            'paiements'               => $paiements,
            'periode'                 => $periode,
            'periodeLabel'            => $periodeLabel,
            'grouping'                => $grouping,
            'nbVinylesVendus'         => $nbVinylesVendus,
            'caMoyenParJour'          => $caMoyenParJour,
            'panierMoyen'             => $panierMoyen,
            'topModelesVendus'        => $topModelesVendus,
            'margeBrute'              => $margeBrute,

            // Vinyles – historique & stock
            'quantiteVinylesStock'       => $quantiteVinylesStock,
            'quantiteVinylesVendus'      => $quantiteVinylesVendus,
            'quantiteVinylesAchetes'     => $quantiteVinylesAchetes,
            'valeurStockAchatVinyles'    => $valeurStockAchatVinyles,
            'coutAchatVinylesVendus'     => $coutAchatVinylesVendus,
            'investissementTotalVinyles' => $investissementTotalVinyles,
            'chiffreAffairesTotal'       => $chiffreAffairesTotal,
            'caTotalPossibleVinyles'     => $caTotalPossibleVinyles,

            // Fonds – stock / historique
            'stockMiroir'                => $stockMiroir,
            'stockDore'                  => $stockDore,
            'quantiteFondsMiroirStock'   => $quantiteFondsMiroirStock,
            'quantiteFondsDoreStock'     => $quantiteFondsDoreStock,
            'quantiteFondsStockTotal'    => $quantiteFondsStockTotal,
            'valeurStockFonds'           => $valeurStockFonds,

            'quantiteFondsMiroirVendus'  => $quantiteFondsMiroirVendus,
            'quantiteFondsDoreVendus'    => $quantiteFondsDoreVendus,
            'quantiteFondsVendusTotal'   => $quantiteFondsVendusTotal,

            'quantiteFondsMiroirAchetes' => $quantiteFondsMiroirAchetes,
            'quantiteFondsDoreAchetes'   => $quantiteFondsDoreAchetes,
            'quantiteFondsAchetesTotal'  => $quantiteFondsAchetesTotal,

            'coutAchatFondsVendus'       => $coutAchatFondsVendus,
            'investissementTotalFonds'   => $investissementTotalFonds,

            // Marges
            'margeBruteTotale'           => $margeBruteTotale,
            'tauxMargeBruteTotale'       => $tauxMargeBruteTotale,
            'margeMoyenneParVinyle'      => $margeMoyenneParVinyle,
            'margePotentielleStock'      => $margePotentielleStock,
        ]);
    }
}
