<?php

namespace App\Observers;

use App\Models\Vinyle;
use App\Services\StockMovementService;

class VinyleObserver
{
    /**
     * Handle the Vinyle "created" event.
     */
    public function created(Vinyle $vinyle): void
    {
        // Traçage création avec stock initial
        if ($vinyle->stock > 0) {
            StockMovementService::traceVinyleCreated($vinyle);
        }
    }

    /**
     * Handle the Vinyle "updating" event.
     * Capture l'ancienne valeur du stock
     */
    public function updating(Vinyle $vinyle): void
    {
        // Stocker l'ancienne valeur pour le traitement après sauvegarde
        $vinyle->old_stock = $vinyle->getOriginal('stock');
    }

    /**
     * Handle the Vinyle "updated" event.
     */
    public function updated(Vinyle $vinyle): void
    {
        $oldStock = $vinyle->old_stock ?? $vinyle->getOriginal('stock', $vinyle->stock);
        $newStock = $vinyle->stock;

        if ($oldStock !== $newStock) {
            StockMovementService::traceVinyleStockChanged($vinyle, $oldStock, $newStock);
        }
    }

    /**
     * Handle the Vinyle "deleted" event.
     * Soft delete - on trace comme sortie définitive
     */
    public function deleted(Vinyle $vinyle): void
    {
        if ($vinyle->stock > 0) {
            StockMovementService::sortie(
                'vinyle',
                $vinyle->id,
                $vinyle->stock,
                $vinyle->reference ?? 'VIN-'.str_pad($vinyle->id, 4, '0', STR_PAD_LEFT),
                'Suppression vinyle : ' . $vinyle->titre
            );
        }
    }

    /**
     * Handle the Vinyle "restored" event.
     */
    public function restored(Vinyle $vinyle): void
    {
        if ($vinyle->stock > 0) {
            StockMovementService::traceVinyleCreated($vinyle);
        }
    }

    /**
     * Handle the Vinyle "force deleted" event.
     */
    public function forceDeleted(Vinyle $vinyle): void
    {
        // Déjà traité par deleted si stock > 0
    }
}
