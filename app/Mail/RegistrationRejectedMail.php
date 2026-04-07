<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $transaction;
    public $reason;

    public function __construct(PaymentTransaction $transaction, $reason)
    {
        $this->transaction = $transaction->loadMissing(['competition', 'user', 'registrations.team', 'registrations.competitionClass']);
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ปฏิเสธการชำระเงิน รหัสบิล: ' . $this->transaction->tx_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_rejected',
        );
    }

    public function attachments(): array { return []; }
}