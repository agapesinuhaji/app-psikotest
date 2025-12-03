<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,800" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

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
<body class="bg-[#FDFDFC] text-[#1b1b18] flex items-center justify-center min-h-screen relative overflow-hidden">

    <div class="absolute inset-0 z-0 opacity-40 pointer-events-none">
        
        <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-[blob-move_15s_ease-in-out_infinite_alternate]" 
             style="animation-delay: 1s;">
        </div>

        <div class="absolute bottom-1/4 right-1/4 w-[600px] h-[600px] bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-[blob-move-2_18s_ease-in-out_infinite_alternate]" 
             style="animation-delay: 2s;">
        </div>
        
    </div>

    <header class="w-full max-w-7xl absolute top-0 pt-6 px-6 lg:pt-8 lg:px-8 z-20">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-1.5 text-sm font-medium border rounded-sm text-[#1b1b18] border-transparent hover:border-gray-300">
                        Dashboard
                    </a>
                @endauth
            </nav>
        @endif
    </header>

    <main class="relative z-10 flex flex-col items-center justify-center p-8 text-center max-w-2xl bg-white/60 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/40 transition-opacity duration-750 opacity-100">

        <div class="mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-blue-600/10 p-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-blue-600">
                <path fill-rule="evenodd" d="M2.25 6a3 3 0 013-3h15.75a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.902 3.75a.75.75 0 00-.53.22L3 13.5l1.623 1.622a.75.75 0 001.06-.002L9.75 12l4.603 4.603a.75.75 0 101.06-1.06L10.811 10.5l4.689-4.689a.75.75 0 00-1.06-1.06L9.75 9.444 5.756 5.449a.75.75 0 00-1.06 1.06L8.434 10.5l-4.242 4.242a.75.75 0 00.53 1.288z" clip-rule="evenodd" />
            </svg>
        </div>

        <h1 class="mb-2 text-4xl lg:text-3xl font-extrabold tracking-tight text-[#1b1b18]">
            {{ config('app.name', 'PEXRA ANALYTICS') }}
        </h1>

        <p class="mb-10 text-lg lg:text-xl text-[#706f6c] font-light max-w-md">
            Solusi terdepan untuk <strong>manajemen data yang efisien</strong> dan pengambilan keputusan strategis.
        </p>

        @if (Route::has('login'))
            <a
                href="{{ route('login') }}"
                class="inline-block px-12 py-3.5 text-xl font-extrabold tracking-wide text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full shadow-xl transition-all duration-300 transform hover:scale-105 hover:shadow-blue-500/50"
            >
                Mulai Sekarang
            </a>
        @endif

    </main>

    <footer class="absolute bottom-0 text-xs text-[#706f6c] py-4 z-10">
        &copy; {{ date('Y') }} {{ config('app.name', 'Pexra Analytics') }}. All Rights Reserved.
    </footer>

</body>
</html>
