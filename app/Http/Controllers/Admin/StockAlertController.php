<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use Illuminate\Http\Request;

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
