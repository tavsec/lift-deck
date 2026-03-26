<?php

namespace App\Mail;

use App\Models\RewardRedemption;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RewardRedeemedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RewardRedemption $redemption,
    ) {
        $coachLocale = $this->redemption->user->coach?->locale ?? 'en';
        $this->locale($coachLocale);
    }

    public function envelope(): Envelope
    {
        $clientName = $this->redemption->user->name;

        return new Envelope(
            subject: __('emails.reward_redeemed.subject', ['name' => $clientName]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.reward-redeemed',
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
