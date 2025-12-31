<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Vinyle;
use App\Models\Fond;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Récupère (ou crée) le panier de l'utilisateur courant
     */
    public function getCart(): Cart
    {
        $sessionId = session()->getId();

        if (Auth::check()) {
            // 1 seul panier par user_id (index unique unique_user_cart)
            $cart = Cart::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                ],
                [
                    'session_id' => $sessionId,          // optionnel
                    'expires_at' => now()->addHours(2),
                ]
            );
        } else {
            // 1 seul panier par session_id invité
            $cart = Cart::firstOrCreate(
                [
                    'session_id' => $sessionId,
                ],
                [
                    'expires_at' => now()->addHours(2),
                ]
            );
        }

        // Pour les anciens paniers sans expires_at
        if (is_null($cart->expires_at)) {
            $cart->expires_at = now()->addHours(2);
            $cart->save();
        }

        return $cart;
    }


    /**
     * Ajouter un vinyle au panier
     */
    public function addVinyle(int $vinyleId, int $quantite = 1, string $fondType = 'standard'): CartItem
    {
        $vinyle = Vinyle::findOrFail($vinyleId);

        if ($quantite <= 0) {
            throw new \Exception("La quantité doit être supérieure à 0");
        }

        // --- Suppléments comme en boutique ---
        $fondSupplements = [
            'standard' => 0,
            'miroir'   => 8,
            'dore'     => 13,
        ];

        // --- Vérif/chargement du fond (miroir/doré) ---
        $fondModel = null;
        if (in_array($fondType, ['miroir', 'dore'])) {
            $fondModel = Fond::where('type', $fondType)->first();

            if (!$fondModel || $fondModel->quantite < $quantite) {
                throw new \Exception("Stock insuffisant de fonds {$fondType} pour {$vinyle->nom}");
            }
        }

        $fondId = $fondModel?->id; // null pour standard

        // --- Prix unitaire ---
        $supplement   = $fondSupplements[$fondType] ?? 0;
        $prixUnitaire = $vinyle->prix + $supplement;

        $cart = $this->getCart();

        // --- Chercher si même vinyle + même fond existent déjà dans le panier ---
        $cartItem = $cart->items()
            ->where('vinyle_id', $vinyleId)
            ->where('fond_id', $fondId)   // on utilise fond_id, PAS "fond"
            ->first();

        if ($cartItem) {
            // Quantité totale après ajout
            $nouvelleQuantite = $cartItem->quantite + $quantite;

            if ($vinyle->quantite < $nouvelleQuantite) {
                throw new \Exception("Stock insuffisant pour {$vinyle->nom} (disponible : {$vinyle->quantite})");
            }

            // Vérif stock fond si nécessaire
            if ($fondModel && $fondModel->quantite < $nouvelleQuantite) {
                throw new \Exception("Stock insuffisant de fonds {$fondType}");
            }

            $cartItem->update([
                'quantite'      => $nouvelleQuantite,
                'prix_unitaire' => $prixUnitaire,
            ]);
        } else {
            // Vérif stock pour la quantité demandée
            if ($vinyle->quantite < $quantite) {
                throw new \Exception("Stock insuffisant pour {$vinyle->nom} (disponible : {$vinyle->quantite})");
            }

            if ($fondModel && $fondModel->quantite < $quantite) {
                throw new \Exception("Stock insuffisant de fonds {$fondType}");
            }

            $cartItem = $cart->items()->create([
                'vinyle_id'     => $vinyleId,
                'fond_id'       => $fondId,
                'quantite'      => $quantite,
                'prix_unitaire' => $prixUnitaire,
            ]);
        }

        return $cartItem->load(['vinyle', 'fond']);
    }

    /**
     * Met à jour la quantité d'un item du panier
     */
    public function updateQuantite(int $itemId, int $quantite): void
    {
        if ($quantite <= 0) {
            throw new \Exception("La quantité doit être supérieure à 0");
        }

        $cart = $this->getCart();

        /** @var CartItem|null $item */
        $item = $cart->items()
            ->with(['vinyle', 'fond'])
            ->whereKey($itemId)
            ->first();

        if (!$item) {
            throw new \Exception("Article introuvable dans le panier.");
        }

        $vinyle = $item->vinyle;
        if (!$vinyle) {
            throw new \Exception("Vinyle introuvable pour cet article.");
        }

        if ($vinyle->quantite < $quantite) {
            throw new \Exception("Stock insuffisant pour {$vinyle->nom} (disponible : {$vinyle->quantite}).");
        }

        if ($item->fond && $item->fond->quantite < $quantite) {
            throw new \Exception("Stock insuffisant de fonds {$item->fond->type}.");
        }

        $item->update([
            'quantite' => $quantite,
        ]);
    }

    /**
     * Supprimer un item du panier
     */
    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();
        $cart->items()->whereKey($itemId)->delete();
    }

    /**
     * Vider le panier
     */
    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    /**
     * Nombre total d'articles dans le panier
     */
    public function count(): int
    {
        return $this->getCart()
            ->items()
            ->sum('quantite');
    }

    /**
     * Vérifie le stock des vinyles/fonds pour tous les items du panier
     * Retourne un tableau de messages d'erreur (à afficher dans la vue)
     */
    public function checkStock(): array
    {
        $cart = $this->getCart();

        $errors = [];

        $items = $cart->items()->with(['vinyle', 'fond'])->get();

        foreach ($items as $item) {
            $vinyle = $item->vinyle;

            if ($vinyle && $vinyle->quantite < $item->quantite) {
                $errors[] = "Stock insuffisant pour {$vinyle->nom} (demandé : {$item->quantite}, disponible : {$vinyle->quantite}).";
            }

            if ($item->fond && $item->fond->quantite < $item->quantite) {
                $errors[] = "Stock insuffisant pour le fond {$item->fond->type} sur {$vinyle->nom} (demandé : {$item->quantite}, disponible : {$item->fond->quantite}).";
            }
        }

        return $errors;
    }
}
