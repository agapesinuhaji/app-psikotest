<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="overflow-hidden">
                <div class="p-6 sm:p-8 space-y-6 text-[#1b1b18]">

                    <h2 class="text-2xl font-extrabold ">
                        Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!
                    </h2>

                </div>
            </div>

            {{-- Card Ujian --}}
            <div class="mt-8">
                <h3 class="text-2xl font-extrabold border-b pb-2 border-gray-200">
                    Ujian Anda
                </h3>

                @php
                    $test = $clientTest ?? null;
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">

                    {{-- ========================================
                        CARD UJIAN 
                    ========================================= --}}
                    @foreach ($spm as $item)
                        <div class="p-4 bg-gray-50 rounded-lg shadow">
                            <img src="{{ asset('storage/' . $item->photo) }}" class="w-full h-40 object-cover rounded-lg mb-4">

                            <p class="text-lg font-bold text-gray-800">Ujian {{ $item->name }}</p>

                            <p class="text-base font-medium mt-4 text-gray-600">Deskripsi</p>
                            <p class="text-base text-gray-700">
                                {{ $item->description }}
                            </p>

                            <p class="text-base font-medium mt-4 text-gray-600">Durasi</p>
                            <p class="text-base font-bold text-green-600">{{ $item->duration }} Menit</p>

                            <div class="mt-6">

                                {{-- Jika user belum punya row client_test --}}
                                @if(!$test)
                                    <button class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                                        Anda belum memiliki akses ujian
                                    </button>

                                {{-- Jika spm_start_at masih kosong → tampilkan tombol mulai --}}
                                @elseif(is_null($test->spm_start_at))
                                    <a href="{{ route('spm.start') }}"
                                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                        Mulai Test
                                    </a>


                                {{-- Jika spm_start_at sudah terisi --}}
                                @else
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg cursor-not-allowed">
                                        Ujian telah dilewati
                                    </button>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    @foreach ($papiKostick as $item)
                        <div class="p-4 bg-gray-50 rounded-lg shadow">
                            <img src="{{ asset('storage/' . $item->photo) }}" class="w-full h-40 object-cover rounded-lg mb-4">

                            <p class="text-lg font-bold text-gray-800">Ujian {{ $item->name }}</p>

                            <p class="text-base font-medium mt-4 text-gray-600">Deskripsi</p>
                            <p class="text-base text-gray-700">
                                {{ $item->description }}
                            </p>

                            <p class="text-base font-medium mt-4 text-gray-600">Durasi</p>
                            <p class="text-base font-bold text-green-600">{{ $item->duration }} Menit</p>

                            <div class="mt-6">

                                {{-- Jika user belum punya row client_test --}}
                                @if(!$test)
                                    <button class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                                        Anda belum memiliki akses ujian
                                    </button>

                                {{-- Jika spm_start_at masih kosong → tampilkan tombol mulai --}}
                                @elseif(is_null($test->papikostick_start_at))
                                    <a href="/papikostick"
                                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                        Mulai Test
                                    </a>

                                {{-- Jika spm_start_at sudah terisi --}}
                                @else
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg cursor-not-allowed">
                                        Ujian telah dilewati
                                    </button>
                                @endif

                            </div>
                        </div>
                    @endforeach
                   

                        




                </div>

            </div>


        </div>
    </div>
</x-app-layout>
