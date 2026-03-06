<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
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
        $cart = $this->cartService->getCart();

        // Vérifier que le panier n'est pas vide
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide. Ajoutez des vinyles avant de commander.');
        }

        // Récupérer les adresses de l'utilisateur connecté
        $addresses = [];
        if (Auth::check()) {
            $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        }

        return view('orders.create', [
            'cart' => $cart,
            'addresses' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'nullable|exists:addresses,id',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            'code_postal' => 'required|string|max:10',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:2',
            'instructions' => 'nullable|string|max:500',
            'save_address' => 'nullable|boolean',
            'address_label' => 'nullable|string|max:100',
            
            // Facturation
            'use_same_address' => 'nullable|boolean',
            'facturation_nom' => 'nullable|string|max:255',
            'facturation_email' => 'nullable|email|max:255',
            'facturation_telephone' => 'nullable|string|max:20',
            'facturation_adresse' => 'nullable|string|max:500',
            'facturation_code_postal' => 'nullable|string|max:10',
            'facturation_ville' => 'nullable|string|max:255',
            'facturation_pays' => 'nullable|string|max:2',
        ]);

        // Préparer les données de livraison
        $shipping = [
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'code_postal' => $validated['code_postal'],
            'ville' => $validated['ville'],
            'pays' => $validated['pays'],
            'instructions' => $validated['instructions'] ?? null,
        ];

        // Sauvegarder l'adresse si demandé
        if (Auth::check() && ($request->input('save_address') || $request->filled('address_id'))) {
            $addressData = array_merge($shipping, [
                'user_id' => Auth::id(),
                'label' => $validated['address_label'] ?? 'Nouvelle adresse',
            ]);

            if ($request->filled('address_id')) {
                // Mettre à jour une adresse existante
                $address = Address::findOrFail($validated['address_id']);
                $address->update($addressData);
            } elseif ($request->input('save_address')) {
                // Créer une nouvelle adresse
                Address::create($addressData);
            }
        }

        // Préparer les données de facturation
        $billing = $shipping; // Par défaut, même adresse
        
        if (!$request->input('use_same_address', true)) {
            $billing = [
                'nom' => $validated['facturation_nom'] ?? $shipping['nom'],
                'email' => $validated['facturation_email'] ?? $shipping['email'],
                'telephone' => $validated['facturation_telephone'] ?? $shipping['telephone'],
                'adresse' => $validated['facturation_adresse'] ?? $shipping['adresse'],
                'code_postal' => $validated['facturation_code_postal'] ?? $shipping['code_postal'],
                'ville' => $validated['facturation_ville'] ?? $shipping['ville'],
                'pays' => $validated['facturation_pays'] ?? $shipping['pays'],
            ];
        }

        // Stocker les infos en session
        Session::put('order_shipping', $shipping);
        Session::put('order_billing', $billing);

        return redirect()->route('orders.payment');
    }

    public function payment()
    {
        $cart = $this->cartService->getCart();
        $shipping = Session::get('order_shipping');
        $billing = Session::get('order_billing');

        // Vérifier que le panier n'est pas vide
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide. Ajoutez des vinyles avant de commander.');
        }

        // Vérifier que les infos de livraison existent
        if (!$shipping) {
            return redirect()->route('orders.create')
                ->with('error', 'Veuillez d\'abord renseigner vos informations de livraison.');
        }

        return view('orders.payment', [
            'cart' => $cart,
            'shipping' => $shipping,
            'billing' => $billing ?? $shipping,
        ]);
    }

    public function confirm(Request $request)
    {
        // Vérifier que le panier n'est pas vide
        $cart = $this->cartService->getCart();
        
        // Charger les relations vinyle pour chaque item
        $cart->items->load('vinyle');
        
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Vérifier que les infos de commande existent
        $shipping = Session::get('order_shipping');
        if (!$shipping) {
            return redirect()->route('orders.create')
                ->with('error', 'Veuillez renseigner vos informations de livraison.');
        }
        
        $billing = Session::get('order_billing') ?? $shipping;

        // Générer un numéro de commande unique
        $numeroCommande = 'CMD-' . date('Y') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

        // Créer la commande en base de données
        $order = \App\Models\Order::create([
            'numero_commande' => $numeroCommande,
            'user_id' => Auth::id(),
            'statut' => 'en_attente',
            'total' => $cart->total,
            'nom' => $shipping['nom'],
            'prenom' => $shipping['nom'], // On utilise nom comme prénom par défaut
            'email' => $shipping['email'],
            'telephone' => $shipping['telephone'],
            'adresse' => $shipping['adresse'],
            'code_postal' => $shipping['code_postal'],
            'ville' => $shipping['ville'],
            'shipping_nom' => $shipping['nom'],
            'shipping_prenom' => $shipping['nom'],
            'shipping_email' => $shipping['email'],
            'shipping_telephone' => $shipping['telephone'],
            'shipping_adresse' => $shipping['adresse'],
            'shipping_code_postal' => $shipping['code_postal'],
            'shipping_ville' => $shipping['ville'],
            'shipping_pays' => $shipping['pays'] ?? 'FR',
            'shipping_instructions' => $shipping['instructions'] ?? null,
            'billing_nom' => $billing['nom'],
            'billing_prenom' => $billing['nom'],
            'billing_email' => $billing['email'],
            'billing_telephone' => $billing['telephone'],
            'billing_adresse' => $billing['adresse'],
            'billing_code_postal' => $billing['code_postal'],
            'billing_ville' => $billing['ville'],
            'billing_pays' => $billing['pays'] ?? 'FR',
        ]);

        // Ajouter les articles de la commande
        foreach ($cart->items as $item) {
            // Debug: afficher les infos de l'item
            \Log::info('CartItem debug', [
                'item_id' => $item->id,
                'vinyle_id' => $item->vinyle_id,
                'fond_id' => $item->fond_id,
                'quantite' => $item->quantite,
            ]);
            
            // Récupérer le vinyle directement depuis la base
            if (!$item->vinyle_id) {
                \Log::error('CartItem sans vinyle_id', ['item_id' => $item->id]);
                continue; // Skip les items sans vinyle_id
            }
            
            $vinyle = \App\Models\Vinyle::find($item->vinyle_id);
            
            if (!$vinyle) {
                // Si le vinyle n'existe plus, on skip ou on lance une erreur
                \Log::error('Vinyle non trouvé', ['vinyle_id' => $item->vinyle_id]);
                continue;
            }
            
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'vinyle_id' => $vinyle->id,
                'titre_vinyle' => $vinyle->nom,
                'artiste_vinyle' => $vinyle->nom, // On utilise nom comme artiste par défaut
                'reference_vinyle' => $vinyle->modele ?: $vinyle->nom, // On utilise modele ou nom comme reference
                'quantite' => $item->quantite,
                'prix_unitaire' => $vinyle->prix,
                'total' => $vinyle->prix * $item->quantite,
            ]);
        }

        // ⚠️ NE PAS vider le panier ici - on attend la confirmation du paiement Stripe
        // Le panier sera vidé dans le webhook Stripe ou la page de succès

        // Rediriger vers le checkout Stripe
        return redirect()->route('payment.checkout', ['order_id' => $order->id]);
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
}
