<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $client,
        public User $coach,
    ) {}

    public function envelope(): Envelope
    {
        $gymName = $this->coach->gym_name ?? $this->coach->name;

        return new Envelope(
            subject: "Welcome to {$gymName}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.welcome-client',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
