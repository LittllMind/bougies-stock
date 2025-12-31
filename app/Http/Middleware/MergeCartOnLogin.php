<?php
// app/Http/Middleware/MergeCartOnLogin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CartService;

class MergeCartOnLogin
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Si l'utilisateur vient de se connecter
        if (auth()->check() && session()->has('cart_merge_pending')) {
            $this->cartService->mergeAnonymousCart();
            session()->forget('cart_merge_pending');
        }

        return $next($request);
    }
}
