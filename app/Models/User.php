<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

protected $fillable = [
    'name',
    'email',
    'password',
    'google_id',
    'avatar',
    'has_setup_profile',
    'prefix_th',
    'first_name_th',
    'last_name_th',
    'prefix_en',
    'first_name_en',
    'last_name_en',
    'role',
    'birthday',
    'email_verified_at',
    'phone_number',
    'shirt_size',
];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'has_setup_profile' => 'boolean',
        ];
    }

    /**
     * Accessor สำหรับจัดการ URL ของรูปโปรไฟล์
     * วิธีใช้ใน Blade: {{ auth()->user()->avatar_url }}
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->avatar) {
                    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
                }

                // ถ้าเป็น URL สมบูรณ์ (เช่นจาก Google) ให้ส่งกลับไปเลย
                if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                    return $this->avatar;
                }

                // ถ้าเป็นไฟล์ในระบบของเราเอง
                return asset('storage/' . $this->avatar);
            },
        );
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmail {
            public function toMail($notifiable)
            {
                $url = $this->verificationUrl($notifiable);

                return (new MailMessage)
                    ->subject('ยืนยันอีเมลของคุณ - Easykids Robotics')
                    ->greeting('สวัสดีครับ!')
                    ->line('ขอบคุณที่ร่วมเป็นส่วนหนึ่งของ Easykids Robotics')
                    ->line('กรุณากดปุ่มด้านล่างเพื่อยืนยันอีเมลของคุณครับ')
                    ->action('ยืนยันอีเมลที่นี่', $url)
                    ->line('หากคุณไม่ได้สมัครสมาชิกกับเรา สามารถปล่อยข้ามอีเมลฉบับนี้ได้เลย')
                    ->salutation('ขอแสดงความนับถือ, ทีมงาน Easykids Robotics');
            }
        });
    }

    // เพิ่มฟังก์ชันนี้ลงไปใน User Model ครับ
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isAdminOrStaff()
    {
        return in_array($this->role, ['admin', 'staff']);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}   