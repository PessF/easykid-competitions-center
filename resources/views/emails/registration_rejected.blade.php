<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>แจ้งผลการตรวจสอบหลักฐาน</title>
    <style>
        body { font-family: 'Kanit', Helvetica, Arial, sans-serif; background-color: #f9fafb; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #ef4444; padding: 30px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px; margin-bottom: 20px; }
        .reason-box { background-color: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 8px; padding: 15px 20px; margin-top: 20px; color: #991b1b; }
        .info-item { margin-bottom: 10px; font-size: 15px; }
        .info-item strong { color: #475569; display: inline-block; width: 120px; }
        .member-list { margin-top: 10px; padding-left: 20px; color: #4b5563; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 15px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>พบปัญหาในการตรวจสอบหลักฐาน</h1>
        </div>
        <div class="content">
            <p>เรียน คุณ <strong>{{ $transaction->user->name ?? 'ผู้สมัคร' }}</strong>,</p>
            <p>ทางทีมงานได้ตรวจสอบหลักฐานการชำระเงิน <strong>(รหัสบิล: {{ $transaction->tx_no }})</strong> ของท่านแล้ว แต่พบว่าไม่สามารถอนุมัติได้ในขณะนี้</p>
            
            <div class="reason-box">
                <strong>สาเหตุที่ถูกปฏิเสธ:</strong><br>
                {{ $reason ?? 'เอกสารไม่ครบถ้วนหรือไม่ถูกต้อง กรุณาติดต่อแอดมิน' }}
            </div>

            <div style="background-color: #f1f5f9; border-left: 4px solid #64748b; padding: 15px; margin: 20px 0; color: #334155; font-weight: bold;">
                ยอดชำระสุทธิที่แจ้งโอน: {{ number_format($transaction->total_amount) }} บาท (รวม {{ $transaction->registrations->count() }} ทีม)
            </div>

            <h3 style="margin-top: 25px; font-size: 18px; color: #1e293b;">📋 รายการแข่งขันในบิลนี้</h3>
            
            @foreach($transaction->registrations as $index => $reg)
                <div class="info-box">
                    <h4 style="margin-top: 0; color: #ef4444; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">รายการที่ {{ $index + 1 }}: {{ $reg->team->name }}</h4>
                    <div class="info-item"><strong>รหัสใบสมัคร:</strong> <span style="font-family: monospace; color: #ef4444; font-weight: bold;">{{ $reg->regis_no }}</span></div>
                    <div class="info-item"><strong>รายการแข่งขัน:</strong> {{ $reg->competition->name }}</div>
                    <div class="info-item"><strong>รุ่นการแข่งขัน:</strong> {{ $reg->competitionClass->name }}</div>
                    <div class="info-item"><strong>ค่าสมัคร:</strong> {{ $reg->competitionClass->entry_fee == 0 ? 'ฟรี' : number_format($reg->competitionClass->entry_fee) . ' บาท' }}</div>
                    
                    <h5 style="margin-top: 15px; margin-bottom: 5px; font-size: 14px; color: #475569;">👥 รายชื่อสมาชิกในทีม:</h5>
                    <ul class="member-list" style="margin-top: 0;">
                        @foreach($reg->team->members as $member)
                            <li>{{ $member->first_name_th }} {{ $member->last_name_th }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div style="text-align: center; margin-top: 25px; border-top: 1px dashed #e2e8f0; padding-top: 20px;">
                <p style="font-size: 14px; color: #6b7280;">ใบสมัครของทีมท่านยัง <b>ไม่ถูกยกเลิก</b> กรุณาเข้าสู่ระบบเพื่อแก้ไขหรือแนบสลิปการโอนเงินเข้ามาใหม่อีกครั้งครับ</p>
                <a href="{{ url('/registrations') }}" class="btn">เข้าสู่ระบบเพื่อแก้ไข</a>
            </div>

        </div>
        <div class="footer">
            <p>อีเมลฉบับนี้ส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ</p>
            <p>&copy; {{ date('Y') }} Easykids Robotics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>