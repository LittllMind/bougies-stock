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
        $pending = $request->cookie('cart_merge_pending');
        $source = $request->cookie('cart_merge_source_id');

        \Illuminate\Support\Facades\Log::info('MergeCartOnLogin check', [
            'user_id' => auth()->id() ?? 'none',
            'auth_check' => auth()->check() ? 'true' : 'false',
            'pending_cookie' => $pending ?? 'MISSING',
            'source_cookie' => $source ?? 'MISSING',
            'all_cookies' => $request->cookies->all(),
        ]);

        if (auth()->check() && $pending) {
            $source = $source ?? session()->getId();

            \Illuminate\Support\Facades\Log::info('MergeCartOnLogin triggered', [
                'user_id' => auth()->id(),
                'source_session_id' => $source,
                'current_session_id' => session()->getId(),
            ]);

            $this->cartService->mergeAnonymousCart($source);

            \Illuminate\Support\Facades\Log::info('MergeCartOnLogin finished', [
                'user_id' => auth()->id(),
                'user_cart_count' => $this->cartService->count(),
            ]);
        }

        return $next($request);
    }
}
