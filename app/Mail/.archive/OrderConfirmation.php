<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): \Illuminate\Mail\Envelope
    {
        return new \Illuminate\Mail\Envelope(
            subject: 'Confirmation de votre commande - Les bougies de Séraphie',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): \Illuminate\Mail\Content
    {
        return new \Illuminate\Mail\Content(
            view: 'emails.orders.confirmation',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'items' => $this->order->items,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
