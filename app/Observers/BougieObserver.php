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
        // Tracage creation avec stock initial si > 0
        if ($bougie->quantite > 0) {
            StockMovementService::traceBougieCreated($bougie);
        }
        
        // Creer alerte si stock initial est faible
        $bougie->checkStockAlert();
    }

    /**
     * Handle the Bougie "updated" event.
     */
    public function updated(Bougie $bougie): void
    {
        // Detecter changement de stock avec wasChanged()
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
        if ($bougie->quantite > 0 && !app()->environment('testing')) {
            // Tracer sortie definitive
            StockMovementService::sortie(
                'bougie',
                $bougie->id,
                $bougie->quantite,
                $bougie->reference,
                'Suppression bougie : ' . $bougie->nom_complet
            );
        }
        
        // Important: Observer ne supprime pas les alertes existantes
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
