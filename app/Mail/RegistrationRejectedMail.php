<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $registration;
    public $reason;

    public function __construct(Registration $registration, $reason)
    {
        $this->registration = $registration->loadMissing(['team.members', 'competition', 'competitionClass', 'user']);
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ปฏิเสธการสมัครแข่งขัน: ' . $this->registration->competition->name,
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