<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(PaymentTransaction $transaction)
    {
        $this->transaction = $transaction->loadMissing(['competition', 'user', 'registrations.team', 'registrations.competitionClass']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'อนุมัติการชำระเงิน รหัสบิล: ' . $this->transaction->tx_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_approved',
        );
    }

    public function attachments(): array { return []; }
}