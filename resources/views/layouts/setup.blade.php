<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('images/favicon.png?v=' . time()) }}" type="image/png">

    <title>EasyKids Competitions - ตั้งค่าโปรไฟล์</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kanit:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased bg-[#f8f9fa] dark:bg-[#050505] text-gray-900 leading-relaxed transition-colors duration-300">

    <div class="min-h-screen flex flex-col items-center p-6 py-12">

        <div class="mb-8 animate-in fade-in slide-in-from-top duration-700">
            <img src="{{ asset('images/logo.png') }}"
                class="h-16 w-auto" alt="EasyKids Robotics">
        </div>

        <div class="w-full sm:max-w-[850px] animate-in fade-in duration-1000">
            {{ $slot }}
        </div>

        <footer class="mt-12 text-center">
            <p class="text-[10px] text-gray-400 font-light uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} EasyKids Robotics. All rights reserved.
            </p>
        </footer>
    </div>
</body>

</html>
