<div>

    {{-- ================= POPUP INSTRUKSI ================= --}}
    @if($showPopup)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-2xl">

                <h2 class="text-xl font-bold mb-3">Instruksi Papikostick</h2>

                <p class="text-gray-700 leading-relaxed mb-4">
                    Tes Papikostick bertujuan mengukur aspek kepribadian.
                    Bacalah pernyataan dengan teliti dan pilih jawaban yang paling sesuai.
                </p>

                <ul class="text-gray-700 list-disc ml-6 mb-4">
                    <li>Tidak ada jawaban benar atau salah</li>
                    <li>Jawablah sejujur-jujurnya</li>
                    <li>Waktu akan berjalan otomatis</li>
                    <li>Dilarang menutup atau me-refresh halaman</li>
                </ul>

                <button
                    wire:click="startTest"
                    wire:click="$dispatch('enterFullscreen')"
                    class="w-full bg-blue-600 text-white p-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Mulai Sekarang
                </button>

            </div>
        </div>
    @endif


    {{-- ================= MAIN WRAPPER ================= --}}
    <div class="{{ $showPopup ? 'blur-sm pointer-events-none select-none' : '' }}">

        <div class="max-w-2xl mx-auto mt-10 p-6 bg-white shadow-xl rounded-xl">

            {{-- TIMER --}}
            <div class="text-right text-red-600 font-bold text-xl mb-4" wire:ignore>
                Sisa Waktu: <span id="timerDisplay">120:00</span>
            </div>

            {{-- PROGRESS BAR --}}
            <div class="w-full bg-gray-200 rounded-full h-3 mb-6" wire:ignore>
                <div id="progressBar"
                     class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                     style="width: 0%">
                </div>
            </div>

            {{-- PERTANYAAN --}}
            <p class="text-xl text-gray-800 mb-6 leading-relaxed">
                {!! $question->question !!}
            </p>

            {{-- OPSI --}}
            <div class="grid grid-cols-1 gap-4">
                @foreach($question->options as $opt)
                    <button
                        wire:click.prevent="selectOption({{ $opt->id }})"
                        class="w-full p-4 border rounded-lg font-medium transition 
                               hover:scale-105 hover:shadow-lg 
                               {{ $selectedOption == $opt->id 
                                   ? 'bg-blue-600 text-white border-blue-700' 
                                   : 'bg-gray-100 text-gray-800 border-gray-300' }}">
                        {!! $opt->option !!}
                    </button>
                @endforeach
            </div>

            {{-- TOMBOL SELANJUTNYA --}}
            @if($selectedOption)
                <button
                    wire:click="nextQuestion"
                    class="mt-6 w-full bg-green-600 text-white p-4 rounded-lg font-semibold shadow hover:bg-green-700 transition">
                    Selanjutnya
                </button>
            @endif

        </div>

    </div>


    {{-- ================= POPUP SELESAI ================= --}}
    @if($showFinishPopup)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-xl text-center shadow-2xl">

                <h2 class="text-2xl font-bold mb-3 text-green-600">Terima Kasih!</h2>

                <p class="text-gray-700 text-lg mb-4">
                    Kamu telah menyelesaikan Tes Papikostick.
                </p>

                <button
                    onclick="window.location.href='/dashboard'"
                    class="w-full bg-blue-600 text-white p-3 rounded-lg font-semibold">
                    Kembali ke Dashboard
                </button>

            </div>
        </div>
    @endif



    {{-- ======================= JAVASCRIPT TIMER + FULLSCREEN ======================= --}}
    <script>
        document.addEventListener('livewire:init', () => {

            let timerInterval = null;

            Livewire.on('startTimer', ({ duration }) => {

                let timeLeft = duration;
                const display = document.getElementById('timerDisplay');

                clearInterval(timerInterval);

                timerInterval = setInterval(() => {

                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;

                    display.textContent =
                        String(minutes).padStart(2, '0') + ":" +
                        String(seconds).padStart(2, '0');

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        Livewire.dispatch('forceSubmit');
                    }

                    timeLeft--;

                }, 1000);
            });

            Livewire.on('enterFullscreen', () => {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                }
            });

            Livewire.on('updateProgress', ({ percentage }) => {
                const bar = document.getElementById('progressBar');
                if (bar) bar.style.width = percentage + '%';
            });

        });
    </script>



    {{-- ======================= JAVASCRIPT ANTI-CHEAT ======================= --}}
    <script>
    document.addEventListener("DOMContentLoaded", () => {

        let unloadProtectionEnabled = true;

        /* Cegah refresh */
        window.addEventListener("beforeunload", function (e) {
            if (!unloadProtectionEnabled) return;
            e.preventDefault();
            e.returnValue = "";
        });

        /* Cegah tombol BACK */
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        /* Cegah pindah tab */
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) {
                Livewire.dispatch('forceSubmit');
            }
        });

        /* Cegah buka tab baru */
        window.addEventListener("keydown", function (e) {
            if (e.ctrlKey && (e.key === "t" || e.key === "n")) e.preventDefault();
        });

        window.addEventListener("auxclick", function (e) {
            if (e.button === 1) e.preventDefault();
        });

        /* Cegah ZOOM */
        window.addEventListener("wheel", function (e) {
            if (e.ctrlKey) e.preventDefault();
        }, { passive: false });

        window.addEventListener("keydown", function (e) {
            if (e.ctrlKey && ["+", "-", "0"].includes(e.key)) e.preventDefault();
        });
    });


    /* Matikan proteksi sebelum submit */
    Livewire.on('disableUnloadProtection', () => {
        unloadProtectionEnabled = false;
    });
    </script>


</div>
