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
        
                    <h2 class="text-2xl font-extrabold border-b pb-2 border-gray-200 dark:border-gray-700">
                        Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Bagian Kiri: Detail Profil --}}
                        <div class="space-y-4">

                            {{-- Nama Pengguna --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-400">Nama Lengkap</p>
                                <p class="text-sm font-bold">{{ auth()->user()->name }} </p>
                            </div>
                            
                            {{-- Email Pengguna --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tempat, Tanggal Lahir</p>
                                <p class="text-sm font-bold">{{ auth()->user()->place_of_birth }}, {{ \Carbon\Carbon::parse(auth()->user()->date_of_birth)->translatedFormat('d F Y') }}</p>
                            </div>

                            {{-- ID Pengguna --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Umur</p>
                                <p class="text-sm font-bold">{{ auth()->user()->age  }} Tahun</p>
                            </div>

                        </div>

                        {{-- Bagian Kanan: Status atau Statistik (Contoh) --}}
                        <div class="space-y-4">
                            {{-- Jenis Kelamin --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</p>
                                <p class="text-sm font-bold">{{ auth()->user()->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            </div>

                            {{-- Pendidikan Terakhir --}}
                            <div class="p-3 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendidikan Terakhir</p>
                                <p class="text-sm font-bold">{{ auth()->user()->last_education  }}</p>
                            </div>
                            
                        </div>

                    </div>
                    </div>
            </div>

            {{-- Card Produk Aktif --}}
            <div class="mt-8">
                <h3 class="text-2xl font-extrabold border-b pb-2 border-gray-200 dark:border-gray-700">
                    Ujian Anda
                </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div class="p-4 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg shadow">

                            {{-- Gambar --}}
                            <img src="https://via.placeholder.com/600x300" alt="Gambar Ujian" class="w-full h-40 object-cover rounded-lg mb-4">
                            
                            {{-- Judul --}}
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Ujian Pilihan Ganda</p>

                            {{-- Deskripsi --}}
                            <p class="text-base font-medium mt-4 text-gray-600 dark:text-gray-400">Deskripsi</p>
                            <p class="text-base text-gray-700 dark:text-gray-300">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum sapiente, laborum placeat deserunt repellat tenetur dolorum, eaque facere repellendus doloribus veritatis cumque cupiditate soluta dolores asperiores non quas iure dolore?
                            </p>

                            {{-- Durasi --}}
                            <p class="text-base font-medium mt-4 text-gray-600 dark:text-gray-400">Durasi</p>
                            <p class="text-base font-bold text-green-600">60 Menit</p>

                            {{-- Tombol Mulai --}}
                            <div class="mt-6">
                                <a href="#"
                                    class="inline-block px-4 py-2 bg-blue-600 text-white text-base font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                                    Mulai Test
                                </a>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gray-100/50 dark:bg-[#0a0a0a]/50 rounded-lg shadow">

                            {{-- Gambar --}}
                            <img src="https://via.placeholder.com/600x300" alt="Gambar Ujian" class="w-full h-40 object-cover rounded-lg mb-4">
                            
                            {{-- Judul --}}
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Ujian Pilihan Ganda</p>

                            {{-- Deskripsi --}}
                            <p class="text-base font-medium mt-4 text-gray-600 dark:text-gray-400">Deskripsi</p>
                            <p class="text-base text-gray-700 dark:text-gray-300">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum sapiente, laborum placeat deserunt repellat tenetur dolorum, eaque facere repellendus doloribus veritatis cumque cupiditate soluta dolores asperiores non quas iure dolore?
                            </p>

                            {{-- Durasi --}}
                            <p class="text-base font-medium mt-4 text-gray-600 dark:text-gray-400">Durasi</p>
                            <p class="text-base font-bold text-green-600">60 Menit</p>

                            {{-- Tombol Mulai --}}
                            <div class="mt-6">
                                <a href="#"
                                    class="inline-block px-4 py-2 bg-blue-600 text-white text-base font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                                    Mulai Test
                                </a>
                            </div>
                        </div>
                    </div>


                    {{-- <p class="text-sm mt-4 text-gray-600 dark:text-gray-400">
                        Anda belum memiliki produk aktif.
                    </p> --}}
            </div>

        </div>
    </div>
</x-app-layout>
