<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use App\Models\Vinyle;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,employe']);
    }

    /**
     * Affiche la liste des alertes de stock
     */
    public function index()
    {
        // Alertes actives
        $alerts = StockAlert::with('alertable')
            ->where('statut', 'actif')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcul des alertes en direct depuis les vinyles
        $criticalCount = Vinyle::where('quantite', '<=', 0)->count();
        $lowStockItems = Vinyle::whereRaw('quantite > 0 AND quantite <= seuil_alerte')
            ->get();
        $outOfStockItems = Vinyle::where('quantite', '<=', 0)->get();

        return view('stock-alerts.index', compact(
            'alerts',
            'criticalCount',
            'lowStockItems',
            'outOfStockItems'
        ));
    }

    /**
     * Marque une alerte comme résolue
     */
    public function resolve(StockAlert $alert)
    {
        $alert->marquerResolu();

        return redirect()
            ->route('stock-alerts.index')
            ->with('success', 'Alerte marquée comme résolue');
    }

    /**
     * Historique des alertes résolues
     */
    public function history()
    {
        $alerts = StockAlert::with('alertable')
            ->where('statut', 'resolu')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('stock-alerts.history', compact('alerts'));
    }

    /**
     * Créer manuellement une alerte (optionnel)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alertable_type' => 'required|string',
            'alertable_id' => 'required|integer',
            'quantite_actuelle' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:1',
        ]);

        // Vérifie si alerte existe déjà
        $exists = StockAlert::where('alertable_type', $validated['alertable_type'])
            ->where('alertable_id', $validated['alertable_id'])
            ->where('statut', 'actif')
            ->exists();

        if ($exists) {
            return back()->with('warning', 'Une alerte existe déjà pour cet article');
        }

        StockAlert::create([
            'alertable_type' => $validated['alertable_type'],
            'alertable_id' => $validated['alertable_id'],
            'quantite_actuelle' => $validated['quantite_actuelle'],
            'seuil_alerte' => $validated['seuil_alerte'],
            'statut' => 'actif',
        ]);

        return back()->with('success', 'Alerte créée avec succès');
    }
}
