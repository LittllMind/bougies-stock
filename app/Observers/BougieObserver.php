<?php

namespace App\Observers;

use App\Models\Bougie;
use App\Services\StockMovementService;

class BougieObserver
{
    /**
     * Handle the Bougie "created" event.
     */
    public function created(Bougie $bougie): void
    {
        // Traçage création avec stock initial si > 0
        if ($bougie->quantite > 0) {
            StockMovementService::traceBougieCreated($bougie);
        }
        
        // Créer alerte si stock initial est faible
        $bougie->checkStockAlert();
    }

    /**
     * Handle the Bougie "updated" event.
     * 🔴 CORRECTION BUG: Utiliser originalIsEquivalent pour détecter changement
     */
    public function updated(Bougie $bougie): void
    {
        // Détecter changement de stock avec wasChanged()
        if ($bougie->wasChanged('quantite')) {
            $oldStock = $bougie->getOriginal('quantite');
            $newStock = $bougie->quantite;

            StockMovementService::traceBougieStockChanged($bougie, $oldStock, $newStock);
            $bougie->checkStockAlert();
        }
    }

    /**
     * Handle the Bougie "deleted" event.
     */
    public function deleted(Bougie $bougie): void
    {
        if ($bougie->quantite > 0) {
            // Tracer sortie définitive
            StockMovementService::sortie(
                'bougie',
                $bougie->id,
                $bougie->quantite,
                $bougie->reference,
                'Suppression bougie : ' . $bougie->nom_complet
            );
        }
        
        // >!important: Observer ne supprime pas les alertes existantes
        // Pour maintenir l'historique
    }

    /**
     * Handle the Bougie "restored" event.
     */
    public function restored(Bougie $bougie): void
    {
        // Comme created, mais avec restauration du stock
        if ($bougie->quantite > 0) {
            StockMovementService::traceBougieCreated($bougie);
        }
        $bougie->checkStockAlert();
    }
}
