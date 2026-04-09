<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class OrderApiController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Marquer une commande comme payée et décrémenter le stock
     */
    public function markPaid(Request $request, Order $order): JsonResponse
    {
        // Vérifier que la commande existe
        if (!$order) {
            return response()->json(['error' => 'Commande non trouvée'], 404);
        }

        // Vérifier que la commande est en attente
        if ($order->statut !== 'pending') {
            return response()->json(['error' => 'La commande n\'est pas en attente de paiement'], 400);
        }

        // Utiliser OrderService pour valider le paiement et décrémenter le stock
        $success = $this->orderService->markOrderPaid($order);

        if ($success) {
            return response()->json([
                'message' => 'Paiement confirmé',
                'order_id' => $order->id,
                'statut' => $order->fresh()->statut
            ], 200);
        }

        return response()->json(['error' => 'Erreur lors du traitement'], 500);
    }
}
