<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): \Illuminate\Mail\Envelope
    {
        return new \Illuminate\Mail\Envelope(
            subject: 'Bienvenue chez Les bougies de Séraphie',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): \Illuminate\Mail\Content
    {
        return new \Illuminate\Mail\Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
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
