<?php

namespace App\Http\Controllers;

use App\Services\CartService;

class OrderController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function create()
    {
        $cart = $this->cartService->getCart();

        // plus tard : vérifications, adresse, etc.
        return view('orders.create', [
            'cart' => $cart,
        ]);
    }
}
