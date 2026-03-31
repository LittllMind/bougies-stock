<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ClientDashboardController extends Controller
{
    /**
     * Affiche le dashboard client avec stats et dernière commande
     */
    public function index()
    {
        $user = Auth::user();

        // Stats
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_spent' => Order::where('user_id', $user->id)
                                  ->whereIn('statut', ['paid', 'shipped', 'delivered'])
                                  ->sum('total'),
        ];

        // Dernière commande avec items
        $latestOrder = Order::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->with(['items.bougie'])
                            ->first();

        // Bougie préférée
        $favoriteBougie = null;
        $favoriteOrder = Order::where('user_id', $user->id)
            ->with(['items.bougie'])
            ->get()
            ->flatMap(fn ($order) => $order->items)
            ->groupBy('bougie_id')
            ->sortByDesc(fn ($items) => $items->sum('quantite'))
            ->first();
        
        if ($favoriteOrder && $favoriteOrder->isNotEmpty()) {
            $favoriteBougie = $favoriteOrder->first()->bougie;
        }

        return view('client.dashboard', compact('stats', 'latestOrder', 'favoriteBougie'));
    }
}