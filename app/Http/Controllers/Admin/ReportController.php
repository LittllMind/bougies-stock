<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bougie;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Génère un rapport PDF d'inventaire des bougies
     * Fallback HTML si PDF non disponible
     */
    public function inventoryPDF()
    {
        Gate::authorize('admin');

        $bougies = Bougie::orderBy('nom')->get();
        
        $stats = [
            'total' => $bougies->count(),
            'valeur_stock' => $bougies->sum(function ($b) {
                return $b->quantite * $b->prix;
            }),
            'alertes' => $bougies->filter(function ($b) {
                return $b->quantite <= $b->seuil_alerte;
            })->count(),
        ];

        // Vérifier si DomPDF est installé
        if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            // Mode fallback: page HTML avec CSS print
            return view('admin.reports.inventory-html', [
                'bougies' => $bougies,
                'stats' => $stats,
                'date' => now()->format('d/m/Y H:i'),
            ]);
        }

        // Génération PDF avec DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.inventory', [
            'bougies' => $bougies,
            'stats' => $stats,
            'date' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download('inventaire-bougies-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Génère un rapport financier PDF
     * Fallback HTML si PDF non disponible
     * SEULEMENT pour les admins (pas les employés)
     */
    public function financialPDF(Request $request)
    {
        Gate::authorize('reports');  // Plus restrictif que 'admin'

        $validated = $request->validate([
            'debut' => 'required|date',
            'fin' => 'required|date|after_or_equal:debut',
        ]);

        $debut = $validated['debut'];
        $fin = $validated['fin'];

        $orders = Order::where('statut', 'paid')
            ->whereBetween('created_at', [$debut, $fin])
            ->with('items.bougie')
            ->get();

        $stats = [
            'commandes' => $orders->count(),
            'total_ventes' => $orders->sum('total'),
            'moyenne_commande' => $orders->count() > 0 
                ? $orders->avg('total') 
                : 0,
        ];

        // Vérifier si DomPDF est installé
        if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            return view('admin.reports.financial-html', [
                'orders' => $orders,
                'stats' => $stats,
                'debut' => \Carbon\Carbon::parse($debut)->format('d/m/Y'),
                'fin' => \Carbon\Carbon::parse($fin)->format('d/m/Y'),
                'date_generation' => now()->format('d/m/Y H:i'),
            ]);
        }

        // Génération PDF avec DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.financial', [
            'orders' => $orders,
            'stats' => $stats,
            'debut' => \Carbon\Carbon::parse($debut)->format('d/m/Y'),
            'fin' => \Carbon\Carbon::parse($fin)->format('d/m/Y'),
            'date_generation' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download('rapport-financier-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Affiche la page de sélection des rapports
     */
    public function index()
    {
        Gate::authorize('admin');
        
        return view('admin.reports.index');
    }
}
