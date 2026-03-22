<x-mail::message>
{{-- 1. ลบส่วน Header มาตรฐานออก แล้วใส่โลโก้ของเราแทนตรงนี้ --}}
<div style="text-align: center; width: 100%; margin-top: 20px; margin-bottom: 30px;">
    <img src="https://www.easykidsrobotics.com/wp-content/uploads/2021/07/logo_EasyKids_re3.png" alt="Easykids Robotics" style="width: 200px; max-width: 200px;">
</div>

{{-- 2. Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('เกิดข้อผิดพลาด!')
@else
# @lang('สวัสดีครับ!')
@endif
@endif

{{-- 3. Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- 4. Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success' => 'success',
        'error' => 'error',
        default => 'primary', 
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- 5. Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- 6. Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
ด้วยความเคารพ,<br>
**ทีมงาน {{ config('app.name') }}**
@endif

{{-- 7. Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
หากคุณมีปัญหาในการคลิกปุ่ม "{{ $actionText }}" ให้ก๊อปปี้ลิงก์ด้านล่างนี้ไปวางที่เบราว์เซอร์ของคุณแทนครับ:
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>