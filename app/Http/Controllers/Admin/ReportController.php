<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bougie;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return view('admin.reports.index');
    }

    public function inventoryPdf()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs');
        }
        
        $bougies = Bougie::all();
        $totalValue = $bougies->sum(function ($b) {
            return $b->quantite * $b->prix;
        });
        
        $lowStockCount = $bougies->filter(function ($b) {
            return $b->quantite <= $b->seuil_alerte;
        })->count();
        
        return response()->view('admin.reports.inventory', compact('bougies', 'totalValue', 'lowStockCount'));
    }

    public function financialPdf(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs');
        }

        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('statut', 'paid')
            ->with('items')
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        return response()->view('admin.reports.financial', compact(
            'orders', 'totalRevenue', 'totalOrders', 'startDate', 'endDate'
        ));
    }

    public function alertsPdf()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs');
        }

        $lowStockBougies = Bougie::whereRaw('quantite <= seuil_alerte')->get();

        return response()->view('admin.reports.alerts', compact('lowStockBougies'));
    }

    // Legacy methods pour compatibilité
    public function monthlyReportForm()
    {
        return view('admin.reports.monthly');
    }

    public function generateMonthlyReport(Request $request)
    {
        return redirect()->route('admin.reports.financial.pdf', [
            'start_date' => $request->input('month') . '-01',
            'end_date' => $request->input('month') . '-31',
        ]);
    }

    public function stock()
    {
        return $this->inventoryPdf();
    }

    public function artists()
    {
        abort(404);
    }

    public function exportVinylesInventory()
    {
        return $this->inventoryPdf();
    }

    public function exportFondsInventory()
    {
        abort(404);
    }
}
