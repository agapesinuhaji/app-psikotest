<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 space-y-6 text-[#1b1b18] dark:text-[#EDEDEC]">
        
                    <h2 class="text-3xl font-extrabold border-b pb-2 border-gray-200 dark:border-gray-700">
                        Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Bagian Kiri: Detail Profil --}}
                        <div class="space-y-4">
                            <p class="text-xl font-semibold text-blue-600 dark:text-blue-400">
                                Detail Akun
                            </p>
                            
                            {{-- Nama Pengguna --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Lengkap</p>
                                <p class="text-lg font-bold">{{ auth()->user()->name ?? 'Nama Tidak Tersedia' }}</p>
                            </div>
                            
                            {{-- Email Pengguna --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tempat, Tanggal Lahir</p>
                                <p class="text-lg font-bold break-words">{{ auth()->user()->place_of_birth }}, {{ auth()->user()->date_of_birth }}</p>
                            </div>

                            {{-- ID Pengguna --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Umur</p>
                                <p class="text-sm font-mono text-gray-700 dark:text-gray-300 break-words">{{ auth()->user()->age  }} Tahun</p>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</p>
                                <p class="text-sm font-mono text-gray-700 dark:text-gray-300 break-words">{{ auth()->user()->gender  }}</p>
                            </div>
                        </div>

                        {{-- Bagian Kanan: Status atau Statistik (Contoh) --}}
                        <div class="space-y-4">
                            <p class="text-xl font-semibold text-blue-600 dark:text-blue-400">
                                Status Aplikasi
                            </p>
                            
                            <div class="flex items-center space-x-3 p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                                <p class="font-medium">Status Akun: Aktif & Terverifikasi</p>
                            </div>
                            
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Gabung</p>
                                <p class="font-bold">{{ auth()->user()->created_at->format('d M Y') ?? 'Tanggal Tidak Tersedia' }}</p>
                            </div>

                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg text-sm text-gray-500 dark:text-gray-400 italic">
                                <p>Konten dashboard lainnya akan muncul di bawah ini.</p>
                            </div>
                        </div>

                    </div>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
