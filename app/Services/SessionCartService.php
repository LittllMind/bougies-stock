<?php

namespace App\Services;

use App\Models\Bougie;
use Illuminate\Support\Facades\Session;

/**
 * Service de panier basé sur la session PHP (pour synchronisation avec localStorage)
 * Alternative à CartService qui utilise la BDD
 */
class SessionCartService
{
    public const CART_KEY = 'cart';
    
    /**
     * Récupère le panier depuis la session
     */
    public function getCart(): array
    {
        return Session::get(self::CART_KEY, []);
    }
    
    /**
     * Récupère les articles du panier enrichis avec les données des bougies
     */
    public function getItems(): array
    {
        $cart = $this->getCart();
        $items = [];
        
        foreach ($cart as $item) {
            $bougie = Bougie::where('reference', $item['reference'])->first();
            if ($bougie) {
                $sousTotal = $item['quantite'] * $bougie->prix;
                $items[] = [
                    'reference' => $bougie->reference,
                    'nom' => $bougie->nom,
                    'parfum' => $bougie->parfum,
                    'prix_unitaire' => $bougie->prix,
                    'quantite' => $item['quantite'],
                    'sous_total' => $sousTotal,
                    'bougie_id' => $bougie->id,
                    'image' => $bougie->image_url ?? null,
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
        return array_reduce($items, fn($sum, $item) => $sum + $item['sous_total'], 0);
    }
    
    /**
     * Compte le nombre total d'articles
     */
    public function count(): int
    {
        $cart = $this->getCart();
        return array_reduce($cart, fn($sum, $item) => $sum + $item['quantite'], 0);
    }
    
    /**
     * Ajouter des articles (pour synchronisation)
     */
    public function setItems(array $items): void
    {
        Session::put(self::CART_KEY, $items);
    }
    
    /**
     * Vide le panier
     */
    public function clear(): void
    {
        Session::put(self::CART_KEY, []);
    }
    
    /**
     * Vérifie si le panier est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }
    
    /**
     * Vérifie le stock pour tous les articles
     */
    public function checkStock(): array
    {
        $cart = $this->getCart();
        $errors = [];
        
        foreach ($cart as $item) {
            $bougie = Bougie::where('reference', $item['reference'])->first();
            if ($bougie && $bougie->quantite < $item['quantite']) {
                $errors[] = "Stock insuffisant pour {$bougie->nom} (demandé : {$item['quantite']}, disponible : {$bougie->quantite}).";
            }
        }
        
        return $errors;
    }
}
