<?php

namespace App\Services;

use App\Models\Bougie;
use App\Models\Order;
use App\Models\StockAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class PDFService
{
    /**
     * Génère le rapport d'inventaire complet
     */
    public function generateInventoryReport(): array
    {
        $bougies = Bougie::all();
        
        $stats = [
            'total_bougies' => $bougies->count(),
            'total_valeur_stock' => $bougies->sum(fn($b) => $b->quantite * $b->prix),
            'total_unites' => $bougies->sum('quantite'),
            'produits_en_alerte' => $bougies->where('quantite', '<=', 5)->count(),
            'produits_indisponibles' => $bougies->where('quantite', '=', 0)->count(),
        ];
        
        $alerts = StockAlert::with('stockable')
            ->where('statut', 'active')
            ->get();
            
        return [
            'bougies' => $bougies,
            'stats' => $stats,
            'alerts' => $alerts,
            'generated_at' => now(),
        ];
    }
    
    /**
     * Génère le rapport financier pour une période
     */
    public function generateFinancialReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();
        
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('statut', 'payee')
            ->with('items.bougie')
            ->get();
            
        $stats = [
            'total_revenus' => $orders->sum('total'),
            'nombre_commandes' => $orders->count(),
            'panier_moyen' => $orders->count() > 0 ? $orders->sum('total') / $orders->count() : 0,
            'periode_start' => $startDate,
            'periode_end' => $endDate,
        ];
        
        // Ventes par collection
        $salesByCollection = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->bougie) {
                    $collection = $item->bougie->collection ?? 'Standard';
                    if (!isset($salesByCollection[$collection])) {
                        $salesByCollection[$collection] = [
                            'nom' => $collection,
                            'unites' => 0,
                            'ca' => 0,
                        ];
                    }
                    $salesByCollection[$collection]['unites'] += $item->quantite;
                    $salesByCollection[$collection]['ca'] += $item->prix_unitaire * $item->quantite;
                }
            }
        }
        
        // Top produits
        $topProducts = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->bougie) {
                    $key = $item->bougie->id;
                    if (!isset($topProducts[$key])) {
                        $topProducts[$key] = [
                            'bougie' => $item->bougie,
                            'unites' => 0,
                            'ca' => 0,
                        ];
                    }
                    $topProducts[$key]['unites'] += $item->quantite;
                    $topProducts[$key]['ca'] += $item->prix_unitaire * $item->quantite;
                }
            }
        }
        
        // Trier par CA décroissant
        uasort($topProducts, fn($a, $b) => $b['ca'] <=> $a['ca']);
        
        return [
            'stats' => $stats,
            'orders' => $orders,
            'sales_by_collection' => array_values($salesByCollection),
            'top_products' => array_slice($topProducts, 0, 5, true),
            'generated_at' => now(),
        ];
    }
    
    /**
     * Retourne le HTML pour impression/PDF
     */
    public function renderInventoryHTML($data): string
    {
        return View::make('admin.reports.inventory', $data)->render();
    }
    
    /**
     * Retourne le HTML pour rapport financier
     */
    public function renderFinancialHTML($data): string
    {
        return View::make('admin.reports.financial', $data)->render();
    }
}
