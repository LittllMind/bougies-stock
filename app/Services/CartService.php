<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Bougie;

class CartService
{
    /**
     * Récupère (ou crée) le panier de l'utilisateur courant
     */
    public function getCart(): Cart
    {
        $sessionId = session()->getId();
        if (empty($sessionId)) {
            session()->start();
            $sessionId = session()->getId();
        }

        $user = auth()->user();
        $expiresAt = now()->addHours(2)->format('Y-m-d H:i:s');

        if ($user) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => $sessionId, 'expires_at' => $expiresAt]
            );
        } else {
            $cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['expires_at' => $expiresAt]
            );
        }

        // Pour les anciens paniers sans expires_at
        if (is_null($cart->expires_at)) {
            $cart->expires_at = now()->addHours(2)->format('Y-m-d H:i:s');
            $cart->save();
        }

        return $cart;
    }

    /**
     * Ajouter une bougie au panier
     */
    public function addBougie(int $bougieId, int $quantite = 1): CartItem
    {
        $bougie = Bougie::findOrFail($bougieId);

        if ($quantite <= 0) {
            throw new \Exception("La quantité doit être supérieure à 0");
        }

        if ($bougie->quantite < $quantite) {
            throw new \Exception("Stock insuffisant pour {$bougie->nom} (disponible : {$bougie->quantite})");
        }

        $cart = $this->getCart();

        // Chercher si même bougie existe déjà dans le panier
        $cartItem = $cart->items()
            ->where('bougie_id', $bougieId)
            ->first();

        if ($cartItem) {
            $nouvelleQuantite = $cartItem->quantite + $quantite;

            if ($bougie->quantite < $nouvelleQuantite) {
                throw new \Exception("Stock insuffisant pour {$bougie->nom} (disponible : {$bougie->quantite})");
            }

            $cartItem->update([
                'quantite' => $nouvelleQuantite,
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'bougie_id' => $bougieId,
                'vinyle_id' => null,
                'fond_id' => null,
                'quantite' => $quantite,
                'prix_unitaire' => $bougie->prix,
            ]);
        }

        return $cartItem->load(['bougie']);
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

        $item = $cart->items()
            ->with(['bougie'])
            ->whereKey($itemId)
            ->first();

        if (!$item) {
            throw new \Exception("Article introuvable dans le panier.");
        }

        $bougie = $item->bougie;
        if (!$bougie) {
            throw new \Exception("Bougie introuvable pour cet article.");
        }

        if ($bougie->quantite < $quantite) {
            throw new \Exception("Stock insuffisant pour {$bougie->nom} (disponible : {$bougie->quantite}).");
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
     * Vérifie le stock des bougies pour tous les items du panier
     */
    public function checkStock(): array
    {
        $cart = $this->getCart();
        $errors = [];

        $items = $cart->items()->with(['bougie'])->get();

        foreach ($items as $item) {
            $bougie = $item->bougie;

            if ($bougie && $bougie->quantite < $item->quantite) {
                $errors[] = "Stock insuffisant pour {$bougie->nom} (demandé : {$item->quantite}, disponible : {$bougie->quantite}).";
            }
        }

        return $errors;
    }

    /**
     * Récupère les items formatés pour affichage (format compatible SessionCartService)
     */
    public function getItems(): array
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart->items()->with('bougie')->get() as $item) {
            if ($item->bougie) {
                $items[] = [
                    'id' => $item->id,
                    'bougie_id' => $item->bougie_id,
                    'reference' => $item->bougie->reference,
                    'nom' => $item->bougie->nom,
                    'parfum' => $item->bougie->parfum,
                    'prix_unitaire' => (float) $item->prix_unitaire,
                    'quantite' => $item->quantite,
                    'sous_total' => (float) $item->prix_unitaire * $item->quantite,
                    'image' => $item->bougie->image_url ?? null,
                ];
            }
        }

        return $items;
    }

    /**
     * Calcule le total du panier
     */
    public function getTotal(): float
    {
        $items = $this->getItems();
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['sous_total'] ?? 0;
        }
        return $total;
    }

    /**
     * Fusionne le panier anonyme avec le panier de l'utilisateur connecté
     * @param string|null $sourceSessionId Session ID source (optionnel)
     * @param int|null $anonCartId ID du panier anonyme alternatif (optionnel)
     */
    public function mergeAnonymousCart(?string $sourceSessionId = null, ?int $anonCartId = null): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Récupérer le panier anonyme
        $anonymousCart = null;
        
        if ($anonCartId) {
            $anonymousCart = Cart::where('id', $anonCartId)
                ->whereNull('user_id')
                ->first();
        } elseif ($sourceSessionId) {
            $anonymousCart = Cart::where('session_id', $sourceSessionId)
                ->whereNull('user_id')
                ->first();
        }

        if (!$anonymousCart) {
            return false;
        }

        // Récupérer ou créer le panier utilisateur
        $userCart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['session_id' => session()->getId(), 'expires_at' => now()->addHours(2)]
        );

        // Fusionner les items
        $anonymousItems = $anonymousCart->items()->with('bougie')->get();

        foreach ($anonymousItems as $item) {
            $existingItem = $userCart->items()
                ->where('bougie_id', $item->bougie_id)
                ->first();

            if ($existingItem) {
                // Vérifier le stock disponible
                $bougie = $item->bougie;
                $nouvelleQuantite = $existingItem->quantite + $item->quantite;

                if ($bougie && $bougie->quantite >= $nouvelleQuantite) {
                    $existingItem->update(['quantite' => $nouvelleQuantite]);
                }
            } else {
                // Copier l'item vers le panier utilisateur
                $userCart->items()->create([
                    'bougie_id' => $item->bougie_id,
                    'vinyle_id' => $item->vinyle_id,
                    'fond_id' => $item->fond_id,
                    'quantite' => $item->quantite,
                    'prix_unitaire' => $item->prix_unitaire,
                ]);
            }
        }

        // Supprimer le panier anonyme et ses items
        $anonymousCart->items()->delete();
        $anonymousCart->delete();
        
        return true;
    }
}
