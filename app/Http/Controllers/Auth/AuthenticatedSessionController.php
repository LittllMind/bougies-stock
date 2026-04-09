<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Vérifier si un panier est en attente (avant login)
        $pendingCartItems = $request->cookie('pending_cart_items');
        
        $request->authenticate();

        // Sauvegarder l' ancien session ID pour fusionner le panier
        $previousSessionId = session()->getId();

        $request->session()->regenerate();

        // Fusion automatique du panier anonyme
        Cookie::queue('cart_merge_source_id', $previousSessionId, 0);
        Cookie::queue('cart_merge_pending', 'true', 0);

        // Si des items étaient en attente, les ajouter au panier
        if ($pendingCartItems) {
            $this->addPendingItemsToCart($pendingCartItems);
            Cookie::queue(Cookie::forget('pending_cart_items'));
        }

        // Log pour debugging
        \Illuminate\Support\Facades\Log::info('Cart merge scheduled on login', [
            'user_id' => Auth::id(),
            'previous_session_id' => $previousSessionId,
        ]);

        // Redirection intelligente
        $redirectTo = $this->getRedirectAfterLogin();
        
        return redirect()->to($redirectTo);
    }
    
    /**
     * Récupère l'URL de redirection après login
     * Priorise la conservation du panier et la finalisation de commande
     */
    private function getRedirectAfterLogin(): string
    {
        // Si une redirection spécifique est demandée
        $intended = session()->pull('url.intended');
        if ($intended) {
            return $intended;
        }
        
        // Par défaut, rediriger vers le panier pour permettre checkout
        return '/cart';
    }
    
    /**
     * Ajoute les items en attente au panier après login
     */
    private function addPendingItemsToCart(string $pendingItemsJson): void
    {
        try {
            $items = json_decode($pendingItemsJson, true);
            if (!is_array($items)) {
                return;
            }
            
            $cartService = app(\App\Services\CartService::class);
            
            foreach ($items as $item) {
                if (isset($item['reference']) && isset($item['quantite'])) {
                    $bougie = \App\Models\Bougie::where('reference', $item['reference'])->first();
                    if ($bougie) {
                        $cartService->addBougie($bougie->id, $item['quantite']);
                    }
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Panier en attente ajouté après login', [
                'user_id' => Auth::id(),
                'items_count' => count($items)
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Impossible d\'ajouter le panier en attente: ' . $e->getMessage());
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
