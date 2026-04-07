<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class RegistrationSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(PaymentTransaction $transaction)
    {
        // รับค่าบิลแม่ (Transaction) มาใช้งาน
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ได้รับหลักฐานการชำระเงินแล้ว: ' . $this->transaction->competition->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}