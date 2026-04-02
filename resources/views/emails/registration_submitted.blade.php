<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ยืนยันการสมัครแข่งขัน</title>
    <style>
        body { font-family: 'Kanit', Helvetica, Arial, sans-serif; background-color: #f9fafb; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #2563eb; padding: 30px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px; }
        .info-item { margin-bottom: 10px; font-size: 15px; }
        .info-item strong { color: #475569; display: inline-block; width: 120px; }
        .member-list { margin-top: 10px; padding-left: 20px; color: #4b5563; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 25px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .status-badge { background-color: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 9999px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ใบสมัครเข้าสู่ระบบแล้ว</h1>
        </div>
        <div class="content">
            <p>สวัสดีคุณ <strong>{{ $registration->user->name }}</strong>,</p>
            <p>เราได้รับข้อมูลการสมัครเข้าแข่งขันของคุณเรียบร้อยแล้ว ขณะนี้สถานะใบสมัครของคุณคือ: 
                <span class="status-badge">รอชำระเงินค่าสมัคร</span>
            </p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #1e293b; font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">📋 รายละเอียดการสมัคร</h3>
                <div class="info-item"><strong>รหัสใบสมัคร:</strong> <span style="font-family: monospace; color: #2563eb; font-weight: bold;">{{ $registration->regis_no }}</span></div>
                <div class="info-item"><strong>รายการแข่งขัน:</strong> {{ $registration->competition->name }}</div>
                <div class="info-item"><strong>รุ่นการแข่งขัน:</strong> {{ $registration->competitionClass->name }}</div>
                <div class="info-item"><strong>ชื่อทีม:</strong> {{ $registration->team->name }}</div>
                <div class="info-item"><strong>โรงเรียน/สถาบัน:</strong> {{ $registration->team->school_name ?? 'ไม่ระบุ' }}</div>
            </div>

            <h3 style="margin-top: 25px; font-size: 16px; color: #1e293b;">👥 รายชื่อสมาชิกในทีม</h3>
            <ul class="member-list">
                @foreach($registration->team->members as $member)
                    <li>{{ $member->first_name_th }} {{ $member->last_name_th }}</li>
                @endforeach
            </ul>

            <div style="text-align: center; margin-top: 30px; border-top: 1px dashed #e2e8f0; padding-top: 20px;">
                <p style="font-size: 14px; color: #6b7280;">กรุณาชำระเงินและอัปโหลดหลักฐาน (สลิปโอนเงิน) เพื่อรักษาสิทธิ์ในการแข่งขัน</p>
                {{-- 🚀 ใช้ url ตรงๆ เพื่อป้องกันปัญหา Route Not Defined เหมือนรอบที่แล้วครับ --}}
                <a href="{{ url('/user/registrations') }}" class="btn">อัปโหลดสลิปโอนเงินคลิกที่นี่</a>
            </div>
        </div>
        <div class="footer">
            <p>อีเมลฉบับนี้เป็นการแจ้งเตือนอัตโนมัติจากระบบ **Easykids Robotics** กรุณาอย่าตอบกลับ</p>
            <p>&copy; {{ date('Y') }} Easykids Robotics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>