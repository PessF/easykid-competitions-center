<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ยืนยันการส่งหลักฐานชำระเงิน</title>
    <style>
        body { font-family: 'Kanit', Helvetica, Arial, sans-serif; background-color: #f9fafb; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #2563eb; padding: 30px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: normal; }
        .content { padding: 30px; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px; margin-bottom: 20px; }
        .info-item { margin-bottom: 10px; font-size: 15px; }
        .info-item span.label { color: #475569; display: inline-block; width: 120px; }
        .member-list { margin-top: 10px; padding-left: 20px; color: #4b5563; font-weight: normal; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: normal; margin-top: 15px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .status-badge { background-color: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 9999px; font-size: 13px; border: 1px solid #bfdbfe; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ได้รับหลักฐานการชำระเงินแล้ว</h1>
        </div>
        <div class="content">
            <p>สวัสดีคุณ <strong>{{ $transaction->user->name ?? 'ผู้สมัคร' }}</strong>,</p>
            <p>ระบบได้รับหลักฐานการชำระเงินของคุณเรียบร้อยแล้ว ขณะนี้สถานะบิลของคุณคือ: 
                <span class="status-badge">รอตรวจสอบยอดเงิน</span>
            </p>

            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; color: #1e40af;">
                <span style="display:block; margin-bottom: 5px;"><strong>รหัสบิล:</strong> {{ $transaction->tx_no }}</span>
                <strong>ยอดชำระสุทธิที่แจ้งโอน:</strong> {{ number_format($transaction->total_amount) }} บาท (รวม {{ $transaction->registrations->count() }} ทีม)
            </div>

            <h3 style="margin-top: 25px; font-size: 18px; color: #1e293b; font-weight: normal;">📋 รายการที่รอตรวจสอบ</h3>
            
            @foreach($transaction->registrations as $index => $reg)
                <div class="info-box">
                    <h4 style="margin-top: 0; color: #2563eb; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; font-weight: normal;">รายการที่ {{ $index + 1 }}: {{ $reg->team->name }}</h4>
                    <div class="info-item"><span class="label">รหัสใบสมัคร:</span> <span style="font-family: monospace; color: #2563eb;">{{ $reg->regis_no }}</span></div>
                    <div class="info-item"><span class="label">รายการแข่งขัน:</span> {{ $reg->competition->name }}</div>
                    <div class="info-item"><span class="label">รุ่นการแข่งขัน:</span> {{ $reg->competitionClass->name }}</div>
                    <div class="info-item"><span class="label">ค่าสมัคร:</span> <span style="color: #2563eb;">{{ $reg->competitionClass->entry_fee == 0 ? 'ฟรี' : number_format($reg->competitionClass->entry_fee) . ' บาท' }}</span></div>
                    
                    <h5 style="margin-top: 15px; margin-bottom: 5px; font-size: 14px; color: #475569; font-weight: normal;">👥 รายชื่อสมาชิกในทีม:</h5>
                    <ul class="member-list" style="margin-top: 0;">
                        @foreach($reg->team->members as $member)
                            <li>{{ $member->first_name_th }} {{ $member->last_name_th }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div style="text-align: center; margin-top: 25px; border-top: 1px dashed #e2e8f0; padding-top: 20px;">
                <p style="font-size: 14px; color: #6b7280;">แอดมินจะทำการตรวจสอบหลักฐานการโอนเงินภายใน 24-48 ชั่วโมง และจะแจ้งผลให้อนุมัติให้ทราบทางอีเมลอีกครั้งครับ</p>
                <a href="{{ url('/registrations') }}" class="btn">เช็คสถานะการสมัคร</a>
            </div>
        </div>
        <div class="footer">
            <p>อีเมลฉบับนี้เป็นการแจ้งเตือนอัตโนมัติจากระบบ <strong>Easykids Robotics</strong> กรุณาอย่าตอบกลับ</p>
            <p>&copy; {{ date('Y') }} Easykids Robotics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>