<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bougie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Synchronise le panier depuis localStorage vers session PHP
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.reference' => 'required|string|exists:bougies,reference',
            'items.*.quantite' => 'required|integer|min:1',
        ]);

        // Stocker dans session PHP
        $cartItems = [];
        foreach ($validated['items'] as $item) {
            $cartItems[] = [
                'reference' => $item['reference'],
                'quantite' => $item['quantite'],
            ];
        }
        
        session(['cart' => $cartItems]);
        
        return response()->json([
            'success' => true,
            'message' => 'Panier synchronisé',
            'count' => count($cartItems),
        ]);
    }

    /**
     * Récupère le panier en cours
     */
    public function index(): JsonResponse
    {
        $cart = session('cart', []);
        
        $items = [];
        $total = 0;
        
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
                ];
                $total += $sousTotal;
            }
        }
        
        if (empty($items)) {
            return response()->json([
                'items' => [],
                'total' => 0,
                'count' => 0,
                'message' => 'Votre panier est vide',
            ]);
        }
        
        return response()->json([
            'items' => $items,
            'total' => $total,
            'count' => count($items),
        ]);
    }

    /**
     * Ajoute un article au panier
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference' => 'required|string|exists:bougies,reference',
            'quantite' => 'required|integer|min:1',
        ]);

        $bougie = Bougie::where('reference', $validated['reference'])->first();
        
        // Vérifier le stock disponible
        if ($validated['quantite'] > $bougie->quantite) {
            return response()->json([
                'message' => "La quantité demandée excède le stock disponible ({$bougie->quantite}).",
                'errors' => [
                    'quantite' => [
                        "La quantité demandée excède le stock disponible ({$bougie->quantite})."
                    ]
                ]
            ], 422);
        }

        $cart = session('cart', []);
        
        // Vérifier si l'article existe déjà
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if ($item['reference'] === $validated['reference']) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex !== null) {
            // Mettre à jour la quantité
            $cart[$existingIndex]['quantite'] += $validated['quantite'];
        } else {
            // Ajouter nouvel article
            $cart[] = [
                'reference' => $validated['reference'],
                'quantite' => $validated['quantite'],
            ];
        }
        
        session(['cart' => $cart]);
        
        return response()->json([
            'success' => true,
            'message' => 'Article ajouté au panier',
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Met à jour la quantité d'un article
     */
    public function update(Request $request, string $reference): JsonResponse
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:1',
        ]);

        $bougie = Bougie::where('reference', $reference)->firstOrFail();
        
        // Vérifier le stock disponible
        if ($validated['quantite'] > $bougie->quantite) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$bougie->quantite}",
            ], 422);
        }

        $cart = session('cart', []);
        
        // Trouver l'article
        $found = false;
        foreach ($cart as $index => $item) {
            if ($item['reference'] === $reference) {
                $cart[$index]['quantite'] = $validated['quantite'];
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier',
            ], 404);
        }
        
        session(['cart' => $cart]);
        
        return response()->json([
            'success' => true,
            'message' => 'Quantité mise à jour',
        ]);
    }

    /**
     * Supprime un article du panier
     */
    public function destroy(string $reference): JsonResponse
    {
        $cart = session('cart', []);
        
        // Filtrer l'article
        $cart = array_values(array_filter($cart, function ($item) use ($reference) {
            return $item['reference'] !== $reference;
        }));
        
        session(['cart' => $cart]);
        
        return response()->json([
            'success' => true,
            'message' => 'Article supprimé du panier',
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Vide le panier
     */
    public function clear(): JsonResponse
    {
        session(['cart' => []]);
        
        return response()->json([
            'success' => true,
            'message' => 'Panier vidé',
        ]);
    }
}