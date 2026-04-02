<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $registration;

    /**
     * รับข้อมูลใบสมัครเข้ามาใน Email
     */
    public function __construct(Registration $registration)
    {
        // โหลดข้อมูลความสัมพันธ์เผื่อไว้ จะได้ดึงชื่อทีม/ชื่อรุ่นได้
        $this->registration = $registration->loadMissing(['team.members', 'competition', 'competitionClass', 'user']);
    }

    /**
     * ตั้งชื่อหัวข้ออีเมล (Subject)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ยืนยันการสมัครแข่งขัน: ' . $this->registration->competition->name,
        );
    }

    /**
     * ชี้ไปที่ไฟล์ Blade ที่เราจะใช้วาดหน้าตาอีเมล
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_submitted', // เดี๋ยวเราจะไปสร้างไฟล์นี้กัน
        );
    }

    public function attachments(): array
    {
        return [];
    }
}