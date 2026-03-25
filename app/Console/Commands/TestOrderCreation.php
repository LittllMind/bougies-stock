<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Bougie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestOrderCreation extends Command
{
    protected $signature = 'test:order:creation';
    protected $description = 'Test order creation step by step';

    public function handle(): void
    {
        DB::transaction(function () {
            try {
                // Create test Bougie
                $bougie = Bougie::factory()->create(["titre" => "Test Bougie", "prix" => 50, "reference" => "BOG-999"]);
                $this->info("Bougie created: {$bougie->id}");

                // Create Order
                $order = Order::create([
                    'numero_commande' => 'CMD-TEST-001',
                    'email' => 'test@test.com',
                    'nom' => 'Test',
                    'prenom' => 'User',
                    'telephone' => '0123456789',
                    'adresse' => '123 Test',
                    'code_postal' => '75001',
                    'ville' => 'Paris',
                    'total' => 50,
                    'statut' => 'en_attente'
                ]);
                $this->info("Order created: {$order->id}");

                // Create OrderItem
                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'vinyle_id' => $bougie->id,
                    'quantite' => 1,
                    'prix_unitaire' => 50,
                    'total' => 50,
                    'titre_vinyle' => $bougie->titre ? $bougie->titre : $bougie->nom,
                    'reference_vinyle' => $bougie->reference
                ]);
                $this->info("OrderItem created: {$item->id}");

                DB::rollBack();
                $this->info("Test successful - rolled back");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error: " . $e->getMessage());
                $this->error("In: " . $e->getFile() . ":" . $e->getLine());
            }
        });
    }
}