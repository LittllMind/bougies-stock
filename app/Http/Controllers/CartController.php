<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    /** @var CartService */
    protected $cartService;
    
    /** @var string|null Classe du service de reservation stock */
    protected $reservationServiceClass = null;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    
    /**
     * Définir le service de reservation stock (pour extensions)
     * @param string $class Nom de la classe du service
     */
    protected function setReservationService(?string $class): void
    {
        $this->reservationServiceClass = $class;
    }
    
    /**
     * Annuler les reservations actives pour l'utilisateur connecte
     * Utilise par les extensions comme la reservation de stock
     */
    protected function cancelReservationsForUser(): void
    {
        if (!$this->reservationServiceClass || !auth()->check()) {
            return;
        }
        
        try {
            $serviceClass = $this->reservationServiceClass;
            $serviceClass::cancelForUser(auth()->id());
        } catch (\Throwable $e) {
            // Silencieux: service peut ne pas exister
        }
    }

    /**
     * Afficher le panier
     * Gère aussi la fusion automatique du panier anonyme après login
     */
    public function index(Request $request)
    {
        // Vérifier si une fusion de panier est en attente (après login)
        $this->handlePendingCartMerge();
        
        $cart = $this->cartService->getCart();

        return view('cart.index', [
            'cart'        => $cart,
            'stockErrors' => $this->cartService->checkStock(), // retourne un tableau de messages
        ]);
    }
    
    /**
     * Gère la fusion automatique du panier après login
     */
    private function handlePendingCartMerge()
    {
        // Vérifier si une fusion est en attente
        if (Cookie::get('cart_merge_pending') === 'true') {
            $sourceSessionId = Cookie::get('cart_merge_source_id');
            
            if ($sourceSessionId) {
                try {
                    // Fusionner le panier anonyme avec le panier utilisateur
                    $this->cartService->mergeAnonymousCart($sourceSessionId);
                    
                    \Log::info('Panier anonyme fusionné automatiquement', [
                        'user_id' => auth()->id(),
                        'source_session' => $sourceSessionId
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Impossible de fusionner le panier anonyme: ' . $e->getMessage());
                }
            }
            
            // Supprimer les cookies de fusion
            Cookie::queue(Cookie::forget('cart_merge_source_id'));
            Cookie::queue(Cookie::forget('cart_merge_pending'));
        }
    }

    /**
     * Ajouter une bougie au panier
     */
    public function add(Request $request)
    {
        $data = $request->validate([
            'bougie_id' => 'required|integer|exists:bougies,id',
            'quantite'  => 'required|integer|min:1',
        ]);

        $this->cartService->addBougie(
            $data['bougie_id'],
            $data['quantite']
        );

        return back()->with('success', 'Bougie ajoutée au panier !');
    }

    /**
     * Ajouter une bougie au panier depuis le catalogue (GET/POST)
     */
    public function addFromCatalogue(Request $request)
    {
        $data = $request->validate([
            'bougie_id' => 'required|integer|exists:bougies,id',
            'quantite'  => 'nullable|integer|min:1',
        ]);

        $quantite = $data['quantite'] ?? 1;

        try {
            $this->cartService->addBougie($data['bougie_id'], $quantite);
            return redirect()->route('cart.index')->with('success', 'Bougie ajoutée au panier !');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mettre à jour la quantité d'un item
     */
    public function update(Request $request, int $itemId)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->updateQuantite($itemId, $validated['quantite']);

            return redirect()->back()->with('success', 'Quantité mise à jour');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Supprimer un item du panier
     */
    public function remove(int $itemId)
    {
        try {
            $this->cartService->removeItem($itemId);

            return redirect()->back()->with('success', 'Article supprimé');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Vider le panier
     * Annule aussi les reservations stock si user connecte et reservation service configure
     */
    public function clear()
    {
        // Annuler les reservations active avant de vider le panier
        $this->cancelReservationsForUser();
        
        $this->cartService->clear();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Panier vidé');
    }

    /**
     * Nombre total d'articles (pour badge par ex.)
     */
    public function count()
    {
        return response()->json([
            'count' => $this->cartService->count(),
        ]);
    }

    /**
     * Synchroniser le panier localStorage (Vue.js) → Base de données
     * Utilisé avant le checkout pour transferer les articles du navigateur vers le backend
     */
    public function sync(Request $request)
    {
        // Nécessite authentification pour créer une commande
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez vous connecter pour passer commande',
                'redirect' => '/login'
            ], 401);
        }

        $data = $request->validate([
            'items' => 'required|array',
            'items.*.reference' => 'required|string|exists:bougies,reference',
            'items.*.quantite' => 'required|integer|min:1',
        ]);

        try {
            // Vider le panier existant et recréer avec les articles reçus
            $this->cartService->clear();

            foreach ($data['items'] as $item) {
                $bougie = \App\Models\Bougie::where('reference', $item['reference'])->first();
                
                if ($bougie) {
                    $this->cartService->addBougie($bougie->id, $item['quantite']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Panier synchronisé',
                'count' => $this->cartService->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
