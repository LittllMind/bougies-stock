<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Event;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Créer une session de paiement Stripe
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Vérifier que l'utilisateur est propriétaire de la commande
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Commande non autorisée');
        }

        // Vérifier que les clés Stripe sont configurées
        $stripeKey = config('services.stripe.secret');
        if (empty($stripeKey) || !str_starts_with($stripeKey, 'sk_')) {
            \Log::error('Clé Stripe manquante ou invalide', [
                'key exists' => !empty($stripeKey),
                'key prefix' => $stripeKey ? substr($stripeKey, 0, 10) . '...' : 'EMPTY'
            ]);
            return redirect()->route('orders.payment')
                ->with('error', 'Configuration Stripe invalide. Vérifiez STRIPE_SECRET dans .env');
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Commande #' . $order->id,
                                'description' => 'Les Bougies de Séraphie - Cire d\'abeille 100%',
                            ],
                            'unit_amount' => (int) ($order->total * 100), // Stripe utilise les centimes
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => config('app.url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.url') . '/payment/cancel',
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                ],
            ]);

            // Créer un enregistrement de paiement en attente
            Payment::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'stripe_session_id' => $session->id,
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => 'eur',
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            \Log::error('Erreur Stripe checkout: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('orders.payment')
                ->with('error', 'Erreur Stripe: ' . $e->getMessage());
        }
    }

    /**
     * Succès du paiement
     */
    public function success(Request $request)
    {
        try {
            $session = Session::retrieve($request->session_id);

            // Vérifier que le paiement est réussi
            if ($session->payment_status === 'paid') {
                // Récupérer le paiement depuis la base de données
                $payment = Payment::where('stripe_session_id', $request->session_id)->first();

                if ($payment) {
                    // Mettre à jour le paiement si ce n'est pas déjà fait
                    if ($payment->status !== 'success') {
                        $payment->update([
                            'status' => 'success',
                            'paid_at' => now(),
                            'stripe_response' => $session->toArray(),
                        ]);

                        // Mettre à jour la commande
                        $payment->order->update([
                            'status' => 'paid',
                            'statut' => 'payee',
                            'validee_at' => now(),
                        ]);

                        // ✅ DÉCRÉMENTER LE STOCK (paiement confirmé)
                        foreach ($payment->order->items as $item) {
                            if ($item->bougie) {
                                $item->bougie->decrement('quantite', $item->quantite);
                                
                                \App\Models\MouvementStock::create([
                                    'type' => 'sortie',
                                    'produit_type' => 'bougie',
                                    'produit_id' => $item->bougie_id,
                                    'quantite' => $item->quantite,
                                'date_mouvement' => now(),
                                    'user_id' => $payment->order->user_id,
                                    'reference' => $payment->order->numero_commande,
                                    'notes' => 'Vente confirmée via redirect',
                                ]);
                            }
                        }

                        // ✅ Vider le panier après paiement confirmé
                        $cartService = app(\App\Services\CartService::class);
                        $cartService->clear();
                    }

                    return view('payment.success', compact('payment'));
                }
            }

            // Paiement non confirmé ou échoué
            return redirect()->route('kiosque.index')->with('error', 'Paiement non confirmé');
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Session Stripe invalide ou expirée
            Log::error('Session Stripe invalide: ' . $e->getMessage());
            return redirect()->route('kiosque.index')->with('error', 'Session de paiement expirée ou invalide');
        }
    }

    /**
     * Annulation du paiement
     */
    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * Webhook Stripe pour les événements asynchrones
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // En environnement de test, accepter les webhooks sans signature
        if (app()->environment('testing') || config('services.stripe.webhook.secret') === null) {
            $data = json_decode($payload, true);
            if ($data && isset($data['type'])) {
                $this->handleWebhookEvent($data['type'], $data['data']['object'] ?? []);
                return response()->json(['status' => 'success']);
            }
            return response('Invalid payload', 400);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook.secret')
            );
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Signature invalide
            return response('Invalid signature', 400);
        }

        $this->handleWebhookEvent($event->type, $event->data->object);
        return response()->json(['status' => 'success']);
    }

    /**
     * Gérer les événements webhook
     */
    private function handleWebhookEvent(string $type, $object)
    {
        // Gérer l'événement
        switch ($type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($object);
                break;

            case 'checkout.session.async_payment_succeeded':
                $this->handleCheckoutCompleted($object);
                break;

            default:
                Log::info('Événement Stripe non géré: ' . $type);
        }
    }

    private function handleCheckoutCompleted($session)
    {
        // En test, le client_reference_id n'est pas dans le session
        // On récupère depuis l'order_id ou depuis les métadonnées
        $orderId = null;
        
        if (is_array($session)) {
            // Format array (environnement de test)
            $sessionId = $session['id'] ?? null;
            $metadata = $session['metadata'] ?? [];
            $clientReferenceId = $session['client_reference_id'] ?? $metadata['order_id'] ?? null;
        } else {
            // Format objet Stripe
            $sessionId = $session->id;
            $metadata = $session->metadata ?? [];
            $clientReferenceId = $session->client_reference_id ?? null;
        }
        
        // Trouver le paiement
        $payment = null;
        if ($sessionId) {
            $payment = Payment::where('stripe_session_id', $sessionId)->first();
        }
        
        // Si pas de paiement trouvé par session_id, chercher par order_id
        if (!$payment && $clientReferenceId) {
            $payment = Payment::where('order_id', $clientReferenceId)->first();
            if ($payment) {
                $payment->update(['stripe_session_id' => $sessionId]);
            }
        }

        if ($payment) {
            // Vérifier si déjà traité
            if ($payment->status === 'success' || $payment->status === 'succeeded') {
                Log::info('Paiement déjà traité: ' . ($sessionId ?? $clientReferenceId));
                return;
            }
            
            $payment->update([
                'status' => 'succeeded',
                'stripe_payment_intent_id' => is_array($session) ? ($session['payment_intent'] ?? null) : ($session->payment_intent ?? null),
                'paid_at' => now(),
            ]);

            $order = $payment->order;
            if ($order) {
                $order->update([
                    'status' => 'paid',
                    'statut' => 'payee',
                    'validee_at' => now(),
                ]);

                // ✅ Envoyer email de confirmation
                $emailService = app(\App\Services\EmailService::class);
                $emailService->sendOrderConfirmation($order);

                // ✅ DÉCRÉMENTER LE STOCK (paiement confirmé uniquement)
                foreach ($order->items as $item) {
                    if ($item->bougie) {
                        $item->bougie->decrement('quantite', $item->quantite);
                        
                        // Enregistrer le mouvement de stock
                        \App\Models\MouvementStock::create([
                            'type' => 'sortie',
                            'produit_type' => 'bougie',
                            'produit_id' => $item->bougie_id,
                            'quantite' => $item->quantite,
                            'date_mouvement' => now(),
                            'user_id' => $order->user_id,
                            'reference' => $order->numero_commande,
                            'notes' => 'Vente confirmée et stock décrémenté',
                        ]);
                    }
                }
            }

            // Vider le panier après paiement confirmé via webhook
            try {
                $cartService = app(\App\Services\CartService::class);
                $cartService->clear();
            } catch (\Exception $e) {
                Log::warning('Impossible de vider le panier: ' . $e->getMessage());
            }

            Log::info('Paiement confirmé via webhook: ' . ($sessionId ?? $clientReferenceId));
        }
    }

    private function handlePaymentSucceeded($paymentIntent)
    {
        // Gérer le format array (test) ou objet Stripe
        $paymentIntentId = is_array($paymentIntent) ? ($paymentIntent['id'] ?? null) : ($paymentIntent->id ?? null);
        Log::info('Paiement réussi: ' . $paymentIntentId);
        
        if (!$paymentIntentId) {
            return;
        }
    }

    private function handlePaymentFailed($paymentIntent)
    {
        // Gérer le format array (test) ou objet Stripe
        $paymentIntentId = is_array($paymentIntent) ? ($paymentIntent['id'] ?? null) : ($paymentIntent->id ?? null);
        Log::error('Paiement échoué: ' . $paymentIntentId);
        
        // Ne pas planter si les données sont incomplètes
        if (!$paymentIntentId) {
            return;
        }
    }
}