<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bougie;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Créer une commande depuis le panier
     */
    public function store(Request $request): JsonResponse
    {
        // Vérification panier - utilise la session (comme CartController)
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'message' => 'Le panier est vide'
            ], 400);
        }

        // Validation des données client
        $validator = Validator::make($request->all(), [
            'nom_client' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'notes_client' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Chargement des articles avec les données bougies
        $cartItems = [];
        $total = 0;
        $stockErrors = [];
        
        foreach ($cart as $item) {
            $bougie = Bougie::where('reference', $item['reference'])->first();
            
            if (!$bougie) {
                $stockErrors[] = "Article {$item['reference']} introuvable";
                continue;
            }
            
            if ($bougie->quantite < $item['quantite']) {
                $stockErrors[] = "Stock insuffisant pour {$bougie->nom} (disponible: {$bougie->quantite})";
            }
            
            $sousTotal = $item['quantite'] * $bougie->prix;
            $cartItems[] = [
                'bougie_id' => $bougie->id,
                'nom' => $bougie->nom,
                'reference' => $bougie->reference,
                'quantite' => $item['quantite'],
                'prix_unitaire' => $bougie->prix,
            ];
            $total += $sousTotal;
        }

        if (!empty($stockErrors)) {
            return response()->json([
                'message' => 'Stock insuffisant pour certains articles',
                'errors' => $stockErrors
            ], 422);
        }

        // Création de la commande
        try {
            DB::beginTransaction();

            $order = Order::create([
                'numero_commande' => $this->generateOrderNumber(),
                'nom' => $request->nom_client,
                'prenom' => '', // Champs legacy requis
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'code_postal' => $request->code_postal,
                'ville' => $request->ville,
                'notes_client' => $request->notes_client,
                'total' => $total,
                'statut' => 'en_attente',
                'status' => 'en_attente',
                // Champs legacy requis
                'shipping_nom' => $request->nom_client,
                'shipping_prenom' => '',
                'shipping_email' => $request->email,
                'shipping_adresse' => $request->adresse,
                'shipping_code_postal' => $request->code_postal,
                'shipping_ville' => $request->ville,
                'shipping_pays' => 'FR',
                'billing_nom' => $request->nom_client,
                'billing_prenom' => '',
                'billing_email' => $request->email,
                'billing_adresse' => $request->adresse,
                'billing_code_postal' => $request->code_postal,
                'billing_ville' => $request->ville,
                'billing_pays' => 'FR',
            ]);

            // Création des items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'bougie_id' => $item['bougie_id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'total' => $item['prix_unitaire'] * $item['quantite'],
                    // Informations pour affichage (utilise les champs vinyles legacy)
                    'titre_vinyle' => $item['nom'],
                    'reference_vinyle' => $item['reference'],
                ]);

                // Mise à jour du stock
                $bougie = Bougie::find($item['bougie_id']);
                $bougie->decrement('quantite', $item['quantite']);
            }

            // Vider le panier
            session(['cart' => []]);

            DB::commit();

            return response()->json([
                'order_id' => $order->id,
                'reference' => $order->numero_commande,
                'message' => 'Commande créée avec succès'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Voir une commande par sa référence
     */
    public function show(string $reference): JsonResponse
    {
        $order = Order::where('numero_commande', $reference)
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Commande non trouvée'
            ], 404);
        }

        return response()->json([
            'order' => [
                'id' => $order->id,
                'reference' => $order->numero_commande,
                'nom_client' => $order->nom,
                'email' => $order->email,
                'adresse' => $order->adresse,
                'ville' => $order->ville,
                'code_postal' => $order->code_postal,
                'telephone' => $order->telephone,
                'total' => $order->total,
                'statut' => $order->statut,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items' => $order->items->map(fn($item) => [
                    'nom' => $item->titre_vinyle ?? $item->nom,
                    'reference' => $item->reference_vinyle ?? $item->reference,
                    'quantite' => $item->quantite,
                    'prix_unitaire' => $item->prix_unitaire,
                    'total' => $item->total,
                ])
            ]
        ]);
    }

    /**
     * Générer un numéro de commande unique
     */
    protected function generateOrderNumber(): string
    {
        $year = date('Y');
        $prefix = 'BOG';
        $random = strtoupper(Str::random(6));
        return "{$prefix}-{$year}-{$random}";
    }
}