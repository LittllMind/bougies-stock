<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\EmailService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
        $this->middleware('auth');
    }

    /**
     * Envoyer manuellement l'email de confirmation d'une commande
     */
    public function sendOrderConfirmation(Request $request, int $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Vérifier que l'utilisateur est propriétaire ou admin
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json([
                'error' => 'Non autorisé'
            ], 403);
        }

        try {
            $this->emailService->sendOrderConfirmation($order);
            
            return response()->json([
                'message' => 'Email de confirmation envoyé',
                'order_id' => $order->id,
                'email' => $order->shipping_email ?? $order->user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'envoi de l\'email',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
