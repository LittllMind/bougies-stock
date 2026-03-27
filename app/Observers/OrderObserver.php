<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\EmailService;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Vérifier si le statut a changé vers "paid"
        if ($order->isDirty('status') && $order->status === 'paid') {
            $emailService = app(EmailService::class);
            $emailService->sendOrderConfirmation($order);
        }
    }
    
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Si la commande est créée directement avec statut paid, envoyer email
        if ($order->status === 'paid') {
            $emailService = app(EmailService::class);
            $emailService->sendOrderConfirmation($order);
        }
    }
}
