<?php

namespace App\Services;

use App\Models\MouvementStock;
use App\Models\Vinyle;
use App\Models\Fond;
use Illuminate\Support\Facades\Auth;

class StockMovementService
{
    /**
     * Enregistrer un mouvement d'entrée de stock
     */
    public static function entree(
        string $produitType,
        int $produitId,
        int $quantite,
        ?string $reference = null,
        ?string $notes = null
    ): MouvementStock {
        return MouvementStock::enregistrer(
            'entree',
            $produitType,
            $produitId,
            $quantite,
            Auth::id() ?? 1, // fallback admin
            $reference,
            $notes
        );
    }

    /**
     * Enregistrer un mouvement de sortie de stock
     */
    public static function sortie(
        string $produitType,
        int $produitId,
        int $quantite,
        ?string $reference = null,
        ?string $notes = null
    ): MouvementStock {
        return MouvementStock::enregistrer(
            'sortie',
            $produitType,
            $produitId,
            $quantite,
            Auth::id() ?? 1,
            $reference,
            $notes
        );
    }

    /**
     * Traçage automatique lors création Vinyle
     */
    public static function traceVinyleCreated(Vinyle $vinyle): void
    {
        self::entree(
            'vinyle',
            $vinyle->id,
            $vinyle->stock ?? 0,
            $vinyle->reference ?? 'VIN-'.str_pad($vinyle->id, 4, '0', STR_PAD_LEFT),
            'Création vinyle : ' . $vinyle->titre
        );
    }

    /**
     * Traçage automatique lors modification stock Vinyle
     */
    public static function traceVinyleStockChanged(Vinyle $vinyle, int $oldStock, int $newStock): void
    {
        $diff = $newStock - $oldStock;
        
        if ($diff === 0) return;

        if ($diff > 0) {
            self::entree(
                'vinyle',
                $vinyle->id,
                $diff,
                $vinyle->reference ?? 'VIN-'.str_pad($vinyle->id, 4, '0', STR_PAD_LEFT),
                'Mise à jour stock : ' . $vinyle->titre . ' (' . $oldStock . ' → ' . $newStock . ')'
            );
        } else {
            self::sortie(
                'vinyle',
                $vinyle->id,
                abs($diff),
                $vinyle->reference ?? 'VIN-'.str_pad($vinyle->id, 4, '0', STR_PAD_LEFT),
                'Mise à jour stock : ' . $vinyle->titre . ' (' . $oldStock . ' → ' . $newStock . ')'
            );
        }
    }

    /**
     * Traçage automatique lors modification stock Fond
     */
    public static function traceFondStockChanged(Fond $fond, string $typeField, int $oldQty, int $newQty): void
    {
        $produitType = match($typeField) {
            'miroir' => 'miroir',
            'dore' => 'dore',
            'standard' => 'pochette',
            default => 'fond'
        };

        $diff = $newQty - $oldQty;
        
        if ($diff === 0) return;

        $labels = [
            'miroir' => 'Miroir',
            'dore' => 'Doré', 
            'standard' => 'Standard'
        ];

        if ($diff > 0) {
            self::entree(
                $produitType,
                $fond->id,
                $diff,
                'FOND-'.str_pad($fond->id, 4, '0', STR_PAD_LEFT),
                'Mise à jour stock ' . $labels[$typeField] . ' (' . $oldQty . ' → ' . $newQty . ')'
            );
        } else {
            self::sortie(
                $produitType,
                $fond->id,
                abs($diff),
                'FOND-'.str_pad($fond->id, 4, '0', STR_PAD_LEFT),
                'Mise à jour stock ' . $labels[$typeField] . ' (' . $oldQty . ' → ' . $newQty . ')'
            );
        }
    }

    /**
     * Traçage commande validée (sorties)
     */
    public static function traceCommandeValidee($order): void
    {
        foreach ($order->lignes as $ligne) {
            if ($ligne->vinyle) {
                self::sortie(
                    'vinyle',
                    $ligne->vinyle->id,
                    $ligne->quantite,
                    'CMD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'Commande #' . $order->id . ' - ' . $ligne->vinyle->titre
                );
            }
            
            if ($ligne->fond) {
                $type = match($ligne->fond_type) {
                    'miroir' => 'miroir',
                    'dore' => 'dore',
                    default => 'pochette'
                };
                
                self::sortie(
                    $type,
                    $ligne->fond->id,
                    $ligne->quantite,
                    'CMD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'Commande #' . $order->id . ' - Fond ' . ucfirst($type)
                );
            }
        }
    }
}
