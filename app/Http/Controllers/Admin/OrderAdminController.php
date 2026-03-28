<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,employe']);
    }

    /**
     * Liste toutes les commandes (admin)
     */
    public function index(Request $request)
    {
        $query = Order::with(['items.bougie', 'user'])
            ->orderBy('created_at', 'desc');

        // Filtre par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        // Filtre par source
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        // Recherche par numéro
        if ($request->has('search') && $request->search) {
            $query->where('numero_commande', 'like', '%' . $request->search . '%');
        }

        // Filtre par date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Afficher une commande
     */
    public function show(Order $order)
    {
        $order->load(['items.bougie', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'statut' => 'required|string|in:pending,paid,processing,shipped,delivered,cancelled',
        ]);

        $oldStatut = $order->statut;
        $newStatut = $validated['statut'];

        // Mettre à jour les timestamps selon le statut
        $updates = ['statut' => $newStatut];

        $order->update($updates);

        return redirect()
            ->back()
            ->with('success', "Statut changé de '{$oldStatut}' à '{$newStatut}'");
    }

    /**
     * Annuler une commande
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->statut === 'cancelled') {
            return redirect()->back()->with('error', 'Commande déjà annulée');
        }

        $order->update([
            'statut' => 'cancelled',
            'annulee_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Commande annulée');
    }
}
