<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\EmailService;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    /**
     * Handle the Order "updated" event.
     * NE PAS envoyer d'email ici - géré par PaymentController
     * pour éviter les doublons (webhook + redirect)
     */
    public function updated(Order $order): void
    {
        // Désactivé - logique email déplacée dans PaymentController
        // Le webhook Stripe est la source de vérité unique
    }
    
    /**
     * Handle the Order "created" event.
     * NE PAS envoyer d'email ici
     */
    public function created(Order $order): void
    {
        // Désactivé - Les emails sont envoyés uniquement après
        // confirmation de paiement par Stripe (PaymentController)
    }
}
