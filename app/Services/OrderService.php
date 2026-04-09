<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StockAlert;
use Illuminate\Support\Facades\Notification;
// use App\Notifications\StockAlertNotification;

class OrderService
{
    /**
     * Marque une commande comme payée et effectue toutes les actions associées
     * - Décrémente le stock des bougies
     * - Crée des alertes si stock sous seuil
     * - Enregistre les mouvements de stock
     * - Vide le panier
     * 
     * @param Order $order La commande à marquer comme payée
     * @return bool Succès ou échec
     */
    public function markOrderPaid(Order $order): bool
    {
        if ($order->statut !== 'pending') {
            return false;
        }

        try {
            // Mettre à jour la commande
            $order->update([
                'statut' => 'paid',
                'status' => 'paid',
                'validee_at' => now(),
            ]);

            // Traiter chaque article de la commande
            foreach ($order->items as $item) {
                if ($item->bougie) {
                    $bougie = $item->bougie;
                    
                    // Stock avant la transaction
                    $stockAvant = $bougie->quantite;
                    $quantiteVendue = $item->quantite;
                    $stockApres = $stockAvant - $quantiteVendue;
                    
                    // Décrémenter le stock
                    $bougie->decrement('quantite', $quantiteVendue);

                    // Enregistrer le mouvement de stock
                    \App\Models\MouvementStock::create([
                        'type' => 'sortie',
                        'produit_type' => 'bougie',
                        'produit_id' => $bougie->id,
                        'quantite' => $quantiteVendue,
                        'date_mouvement' => now(),
                        'user_id' => $order->user_id,
                        'reference' => $order->numero_commande,
                        'notes' => 'Vente confirmée - commande #' . $order->id,
                    ]);

                    // L'alerte est gérée automatiquement par BougieObserver via checkStockAlert()
                    // après la décrémentation du stock. Pas besoin de créer manuellement ici.
                }
            }

            // Vider le panier de l'utilisateur
            if ($order->user_id) {
                $cartService = app(CartService::class);
                $cartService->clear();
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
