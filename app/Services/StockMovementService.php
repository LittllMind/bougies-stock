<?php

namespace App\Services;

use App\Models\MouvementStock;
use App\Models\Fond;
use App\Models\Vinyle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockMovementService
{
    /**
     * Incrémenter le stock d'un fond et créer un mouvement
     */
    public static function incrementerFond(Fond $fond, int $quantite = 1, ?string $reference = null, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($fond, $quantite, $reference, $notes) {
            // Mise à jour stock
            $fond->quantite += $quantite;
            $fond->save();

            // Création du mouvement
            MouvementStock::enregistrer(
                type: 'entree',
                produitType: $fond->type, // 'miroir', 'dore', etc.
                produitId: $fond->id,
                quantite: $quantite,
                userId: Auth::id() ?? 1,
                reference: $reference,
                notes: $notes ?? "Incrémentation manuelle du stock {$fond->type}"
            );

            return true;
        });
    }

    /**
     * Décrémenter le stock d'un fond et créer un mouvement
     */
    public static function decrementerFond(Fond $fond, int $quantite = 1, ?string $reference = null, ?string $notes = null): bool
    {
        if ($fond->quantite < $quantite) {
            throw new \Exception('Stock insuffisant');
        }

        return DB::transaction(function () use ($fond, $quantite, $reference, $notes) {
            // Mise à jour stock
            $fond->quantite -= $quantite;
            $fond->save();

            // Création du mouvement
            MouvementStock::enregistrer(
                type: 'sortie',
                produitType: $fond->type,
                produitId: $fond->id,
                quantite: $quantite,
                userId: Auth::id() ?? 1,
                reference: $reference,
                notes: $notes ?? "Décrémentation manuelle du stock {$fond->type}"
            );

            return true;
        });
    }

    /**
     * Enregistrer une entrée de stock (achat fournisseur)
     */
    public static function entreeStock(
        string $produitType,
        int $produitId,
        int $quantite,
        ?string $reference = null,
        ?string $notes = null
    ): bool {
        return DB::transaction(function () use ($produitType, $produitId, $quantite, $reference, $notes) {
            // Mise à jour du produit concerné
            $produit = self::getProduit($produitType, $produitId);
            if ($produit) {
                $produit->quantite += $quantite;
                $produit->save();
            }

            // Création du mouvement
            MouvementStock::enregistrer(
                type: 'entree',
                produitType: $produitType,
                produitId: $produitId,
                quantite: $quantite,
                userId: Auth::id() ?? 1,
                reference: $reference ?? 'ACH-' . now()->format('Ymd'),
                notes: $notes ?? "Réception fournisseur"
            );

            return true;
        });
    }

    /**
     * Enregistrer une sortie de stock (vente)
     */
    public static function sortieStock(
        string $produitType,
        int $produitId,
        int $quantite,
        ?string $reference = null,
        ?string $notes = null
    ): bool {
        return DB::transaction(function () use ($produitType, $produitId, $quantite, $reference, $notes) {
            $produit = self::getProduit($produitType, $produitId);
            
            if ($produit && $produit->quantite < $quantite) {
                throw new \Exception("Stock insuffisant pour {$produitType} #{$produitId}");
            }

            if ($produit) {
                $produit->quantite -= $quantite;
                $produit->save();
            }

            // Création du mouvement
            MouvementStock::enregistrer(
                type: 'sortie',
                produitType: $produitType,
                produitId: $produitId,
                quantite: $quantite,
                userId: Auth::id() ?? 1,
                reference: $reference,
                notes: $notes ?? "Sortie stock"
            );

            return true;
        });
    }

    /**
     * Récupérer un produit selon son type
     */
    private static function getProduit(string $produitType, int $produitId): ?\Illuminate\Database\Eloquent\Model
    {
        return match($produitType) {
            'miroir', 'dore', 'pochette' => Fond::find($produitId),
            'vinyle' => Vinyle::find($produitId),
            default => null,
        };
    }
}