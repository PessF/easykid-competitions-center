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
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px; }
        .reason-box { background-color: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 8px; padding: 15px 20px; margin-top: 20px; color: #991b1b; }
        .info-item { margin-bottom: 10px; font-size: 15px; }
        .info-item strong { color: #475569; display: inline-block; width: 120px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>พบปัญหาในการตรวจสอบหลักฐาน</h1>
        </div>
        <div class="content">
            <p>เรียน คุณ {{ $registration->user->name ?? 'ผู้สมัคร' }},</p>
            <p>ทางทีมงานได้ตรวจสอบหลักฐานการสมัครของทีมท่านในรายการ <strong>{{ $registration->competition->name ?? '-' }}</strong> แต่พบว่าไม่สามารถอนุมัติได้ในขณะนี้</p>
            
            <div class="reason-box">
                <strong>สาเหตุที่ถูกปฏิเสธ:</strong><br>
                {{ $reason ?? 'เอกสารไม่ครบถ้วนหรือไม่ถูกต้อง กรุณาติดต่อแอดมิน' }}
            </div>

            <div class="info-box">
                <div class="info-item"><strong>รหัสใบสมัคร:</strong> {{ $registration->regis_no }}</div>
                <div class="info-item"><strong>รุ่นที่สมัคร:</strong> {{ $registration->competitionClass->name ?? '-' }}</div>
                <div class="info-item"><strong>ชื่อทีม:</strong> {{ $registration->team->name ?? '-' }}</div>
            </div>

            <p style="margin-top: 20px;">กรุณาเข้าสู่ระบบเพื่อตรวจสอบข้อมูล หรือแนบสลิปการโอนเงินเข้ามาใหม่อีกครั้งครับ</p>

        </div>
        <div class="footer">
            <p>อีเมลฉบับนี้ส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ</p>
            <p>&copy; {{ date('Y') }} Easykids Robotics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>