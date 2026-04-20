<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function create()
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();

        // Vérifier que le panier n'est pas vide
        if (empty($items)) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide. Ajoutez des bougies avant de commander.');
        }

        // Récupérer les adresses de l'utilisateur connecté
        $addresses = [];
        if (Auth::check()) {
            $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        }

        // Récupérer l'adresse temporaire de session (pré-remplissage)
        $tempShipping = Session::get('order_shipping');
        $tempBilling = Session::get('order_billing');

        return view('orders.create', [
            'items' => $items,
            'total' => $total,
            'addresses' => $addresses,
            'tempShipping' => $tempShipping,
            'tempBilling' => $tempBilling,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            'code_postal' => 'required|string|max:10',
            'ville' => 'required|string|max:255',
        ]);

        // Préparer les données de livraison
        $shipping = [
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'code_postal' => $validated['code_postal'],
            'ville' => $validated['ville'],
            'pays' => 'FR',
        ];

        // Stocker les infos en session
        Session::put('order_shipping', $shipping);

        return redirect()->route('orders.payment');
    }

    public function payment()
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();
        
        $shipping = Session::get('order_shipping');

        // Vérifier que le panier n'est pas vide
        if (empty($items)) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Vérifier que les infos de livraison existent
        if (!$shipping) {
            return redirect()->route('orders.create')
                ->with('error', 'Veuillez d\'abord renseigner vos informations de livraison.');
        }

        // Vérifier le stock AVANT de créer la commande
        foreach ($items as $item) {
            $bougie = \App\Models\Bougie::find($item['bougie_id']);
            if (!$bougie) {
                return redirect()->route('cart.index')
                    ->with('error', "Produit non disponible.");
            }
            if ($bougie->quantite < $item['quantite']) {
                return redirect()->route('cart.index')
                    ->with('error', "Stock insuffisant pour '{$bougie->nom}'. Disponible: {$bougie->quantite}, Demandé: {$item['quantite']}");
            }
        }

        // Créer la commande (sans décrément stock)
        $order = $this->createOrderFromSession($items, $total, $shipping);
        
        // Stocker l'ID de la commande en session
        Session::put('pending_order_id', $order->id);

        return view('orders.payment', [
            'items' => $items,
            'total' => $total,
            'shipping' => $shipping,
            'order' => $order,
        ]);
    }

    /**
     * Créer une commande à partir des données de session
     */
    private function createOrderFromSession(array $items, float $total, array $shipping)
    {
        $maxRetries = 5;
        $retryDelayMs = 50;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Générer un numéro de commande unique
                $numeroCommande = $this->generateUniqueOrderNumber();

                // Créer la commande
                $order = Order::create([
                    'numero_commande' => $numeroCommande,
                    'user_id' => Auth::id(),
                    'statut' => 'pending',
                    'total' => $total,
                    'nom' => $shipping['nom'],
                    'email' => $shipping['email'],
                    'telephone' => $shipping['telephone'],
                    'adresse' => $shipping['adresse'],
                    'code_postal' => $shipping['code_postal'],
                    'ville' => $shipping['ville'],
                    'pays' => $shipping['pays'] ?? 'FR',
                ]);

                // Ajouter les articles
                foreach ($items as $item) {
                    $bougie = \App\Models\Bougie::find($item['bougie_id']);
                    
                    if (!$bougie) {
                        \Log::error('Bougie non trouvée', ['bougie_id' => $item['bougie_id']]);
                        continue;
                    }
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'bougie_id' => $bougie->id,
                        'quantite' => $item['quantite'],
                        'prix_unitaire' => $item['prix_unitaire'],
                        'total' => $item['sous_total'],
                    ]);
                    
                    // NOTE: Le stock n'est PAS décrémenté ici
                    // Le décrément se fait uniquement lors du paiement confirmé (webhook)
                    // Cela évite de bloquer le stock pour les commandes abandonnées
                    // Une tâche cron nettoie les commandes pending vieilles de +2h
                }

                return $order;
                
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000 && $attempt < $maxRetries) {
                    usleep($retryDelayMs * 1000 * $attempt);
                    continue;
                }
                throw $e;
            }
        }
        
        throw new \Exception('Impossible de créer la commande après ' . $maxRetries . ' tentatives');
    }

    /**
     * Générer un numéro de commande unique
     */
    private function generateUniqueOrderNumber(): string
    {
        $year = date('Y');
        $timestamp = microtime(true);
        $random = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
        
        return sprintf('CMD-%s-%s-%s', $year, substr(md5($timestamp . $random), 0, 6), $random);
    }

    /**
     * Page de succès après confirmation de commande
     */
    public function success()
    {
        return view('orders.success');
    }

    /**
     * Page d'annulation de commande
     */
    public function cancel()
    {
        return view('orders.cancel')
            ->with('error', 'Votre commande a été annulée.');
    }

    /**
     * Afficher les commandes de l'utilisateur connecté (Mes commandes)
     */
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('items.bougie')
            ->paginate(10);

        return view('orders.my-orders', compact('orders'));
    }
}
