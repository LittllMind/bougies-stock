<?php

namespace App\Http\Controllers;

use App\Models\Fond;
use Illuminate\Http\Request;

class FondController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des fonds - accessible Admin et Employé
     */
    public function index()
    {
        $fonds = Fond::all()->map(function ($fond) {
            return [
                'id' => $fond->id,
                'type' => $fond->type,
                'visuel' => $fond->visuel,
                'quantite' => $fond->quantite,
                'prix_achat' => $fond->prix_achat,
                'montant_stock' => $fond->montant_stock,
                'prix_vente' => $fond->prix_vente,
                'valeur_stock' => $fond->valeur_stock,
                'marge' => $fond->marge,
                'status' => $fond->status,
                'status_class' => $fond->status_class,
            ];
        });

        // Totaux
        $totaux = [
            'quantite_totale' => $fonds->sum('quantite'),
            'montant_investi' => $fonds->sum('montant_stock'),
            'valeur_totale' => $fonds->sum('valeur_stock'),
            'marge_totale' => $fonds->sum('marge'),
        ];

        return view('fonds.index', compact('fonds', 'totaux'));
    }

    /**
     * Mise à jour du stock - Admin uniquement
     */
    public function updateStock(Request $request, Fond $fond)
    {
        // Vérification admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('fonds.index')
                ->with('error', 'Action réservée aux administrateurs');
        }

        $validated = $request->validate([
            'action' => 'required|in:increment,decrement,set',
            'quantite' => 'required|integer|min:0',
        ]);

        $quantite = $validated['quantite'];

        switch ($validated['action']) {
            case 'increment':
                $fond->quantite += $quantite;
                $message = "+{$quantite} {$fond->type} ajoutés";
                break;
            case 'decrement':
                if ($fond->quantite < $quantite) {
                    return redirect()->route('fonds.index')
                        ->with('error', 'Stock insuffisant pour cette sortie');
                }
                $fond->quantite -= $quantite;
                $message = "-{$quantite} {$fond->type} retirés";
                break;
            case 'set':
                $fond->quantite = $quantite;
                $message = "Stock {$fond->type} fixé à {$quantite}";
                break;
        }

        $fond->save();

        // TODO: Créer mouvement stock ici quand T9 sera implémenté

        return redirect()->route('fonds.index')
            ->with('success', $message);
    }

    /**
     * Mise à jour des prix - Admin uniquement
     */
    public function updatePrix(Request $request, Fond $fond)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('fonds.index')
                ->with('error', 'Action réservée aux administrateurs');
        }

        $validated = $request->validate([
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        $fond->update($validated);

        return redirect()->route('fonds.index')
            ->with('success', 'Prix mis à jour pour ' . $fond->type);
    }
}
