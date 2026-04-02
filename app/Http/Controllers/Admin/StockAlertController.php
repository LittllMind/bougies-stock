<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockAlertController extends Controller
{
    public function index()
    {
        $alertes = StockAlert::with('stockable')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $alertesEnAttente = StockAlert::where('resolue', false)->count();
        $alertesResolues = StockAlert::where('resolue', true)->count();

        return view('admin.stock-alerts.index', compact('alertes', 'alertesEnAttente', 'alertesResolues'));
    }

    /**
     * Export CSV des alertes actives (stock bas)
     */
    public function export(Request $request)
    {
        // Récupérer les alertes actives (non résolues, statut actif)
        $query = StockAlert::with('stockable')
            ->where('statut', 'actif')
            ->where('resolue', false);

        // Filtres optionnels
        if ($request->has('type')) {
            $query->parType($request->input('type'));
        }
        if ($request->has('niveau')) {
            $query->parNiveau($request->input('niveau'));
        }
        if ($request->has('periode')) {
            $query->parPeriode($request->input('periode'));
        }

        $alertes = $query->triPriorite()->get();

        $filename = 'alertes-stock-' . now()->format('Y-m-d_His') . '.csv';

        $response = new StreamedResponse(function () use ($alertes) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes
            fputcsv($handle, [
                'ID',
                'Type',
                'Nom Produit',
                'Quantité Actuelle',
                'Seuil Alerte',
                'Niveau',
                'Statut',
                'Date Création',
            ]);

            // Données
            foreach ($alertes as $alerte) {
                $type = class_basename($alerte->stockable_type);
                $nom = $alerte->stockable ? $alerte->stockable->nom : 'N/A';

                fputcsv($handle, [
                    $alerte->id,
                    $type,
                    $nom,
                    $alerte->quantite_actuelle,
                    $alerte->seuil_alerte,
                    $alerte->niveau_label,
                    $alerte->statut,
                    $alerte->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    public function show(StockAlert $stockAlert)
    {
        $stockAlert->load('stockable');
        return view('admin.stock-alerts.show', compact('stockAlert'));
    }

    public function resolve(StockAlert $stockAlert)
    {
        $stockAlert->update([
            'resolue' => true,
            'resolved_at' => now(),
            'statut' => 'resolu',
        ]);

        return redirect()->route('admin.stock-alerts.index')
            ->with('success', 'Alerte marquée comme résolue.');
    }

    public function destroy(StockAlert $stockAlert)
    {
        $stockAlert->delete();

        return redirect()->route('admin.stock-alerts.index')
            ->with('success', 'Alerte supprimée.');
    }
}
