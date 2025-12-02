<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- CSS ANIMASI BLOB (PENTING untuk Background Bergerak) --}}
    <style>
        @keyframes blob-move {
            0% { transform: translate(0, 0) scale(1); }
            30% { transform: translate(-50px, 30px) scale(1.1); }
            60% { transform: translate(30px, -40px) scale(0.95); }
            100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes blob-move-2 {
            0% { transform: translate(0, 0) scale(1); }
            40% { transform: translate(60px, -20px) scale(1.05); }
            75% { transform: translate(-20px, 50px) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }
    </style>
</head>
<body class="font-sans antialiased relative">
    
    {{-- 1. BAGIAN BACKGROUND BERGERAK (Floating Blob) --}}
    {{-- Latar belakang diletakkan di body dan memiliki z-index 0 --}}
    <div class="fixed inset-0 z-0 opacity-40 dark:opacity-20 pointer-events-none bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        
        {{-- Blob 1 (Biru/Ungu) --}}
        <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-[blob-move_15s_ease-in-out_infinite_alternate] starting:opacity-0" 
             style="animation-delay: 1s;">
        </div>

        {{-- Blob 2 (Pink/Fuchsia) --}}
        <div class="absolute bottom-1/4 right-1/4 w-[600px] h-[600px] bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-[blob-move-2_18s_ease-in-out_infinite_alternate] starting:opacity-0" 
             style="animation-delay: 2s;">
        </div>
        
    </div>
    
    {{-- 2. Wraper Konten Utama (Z-index 10 agar di atas background) --}}
    <div class="min-h-screen relative z-10 bg-transparent">
        
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            {{-- Header menggunakan warna putih/hitam solid dan sedikit transparan agar tidak mengganggu --}}
            <header class="bg-white/90 dark:bg-[#161615]/90 shadow backdrop-blur-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="text-[#1b1b18] dark:text-[#EDEDEC]">
            {{ $slot }}
        </main>
    </div>
</body>
</html>