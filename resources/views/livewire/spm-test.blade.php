<div>

    {{-- ================= POPUP INSTRUKSI ================= --}}
    @if($showPopup)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-2xl">

                <h2 class="text-xl font-bold mb-3">Instruksi Tes SPM</h2>

                <p class="text-gray-700 leading-relaxed mb-4">
                    Tes SPM bertujuan mengukur kemampuan mental umum Anda.
                    Bacalah setiap soal dengan teliti dan pilih jawaban yang paling tepat.
                </p>

                <ul class="text-gray-700 list-disc ml-6 mb-4">
                    <li>Waktu berjalan otomatis</li>
                    <li>Dilarang menutup / refresh halaman</li>
                    <li>Dilarang berpindah tab</li>
                </ul>

                <p class="text-gray-700 leading-relaxed mb-4">
                    Apabila terdeteksi melanggar aturan maka ujian akan otomatis berakhir.
                </p>

                <button
                    wire:click="startTest; $dispatch('enterFullscreen')"
                    class="w-full bg-blue-600 text-white p-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Mulai Sekarang
                </button>

            </div>
        </div>
    @endif


    {{-- ================= LOADING OVERLAY SAAT PINDAH SOAL ================= --}}
    <div
        wire:loading
        wire:target="nextQuestion"
        class="fixed inset-0 bg-white/70 backdrop-blur-sm z-50 flex items-center justify-center">

        <div class="flex flex-col items-center gap-4">
            <svg class="animate-spin h-10 w-10 text-blue-600"
                 xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
            </svg>
            <p class="text-gray-700 font-medium">
                Memuat soal berikutnya...
            </p>
        </div>
    </div>


    {{-- ================= MAIN WRAPPER ================= --}}
    <div class="{{ $showPopup ? 'blur-sm pointer-events-none select-none' : '' }}">

        <div class="max-w-5xl mx-auto mt-10 p-8 bg-white shadow-xl rounded-xl">

            {{-- TIMER --}}
            <div class="text-right text-red-600 font-bold text-xl mb-4" wire:ignore>
                Sisa Waktu: <span id="timerDisplay">00:00</span>
            </div>

            {{-- PROGRESS BAR --}}
            <div class="w-full bg-gray-200 rounded-full h-3 mb-6" wire:ignore>
                <div id="progressBar"
                     class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                     style="width: 0%">
                </div>
            </div>

            {{-- ================= PERTANYAAN ================= --}}
            <div wire:loading.remove wire:target="nextQuestion">
                <p class="text-xl text-gray-800 mb-3 leading-relaxed">
                    {!! $question->question !!}
                </p>
            </div>

            {{-- LOADING KHUSUS AREA SOAL --}}
            <div wire:loading wire:target="nextQuestion" class="py-10 text-center">
                <p class="text-gray-500 italic">Memuat soal & gambar...</p>
            </div>

            {{-- ================= OPSI ================= --}}
            <div
                wire:loading.remove
                wire:target="nextQuestion"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                @foreach($question->options as $opt)
                    <button
                        wire:click="selectOption({{ $opt->id }})"
                        class="w-full p-4 border rounded-lg font-medium transition
                            hover:scale-105 hover:shadow-lg
                            {{ $selectedOption == $opt->id
                                ? 'bg-blue-600 text-white border-blue-700'
                                : 'bg-gray-100 text-gray-800 border-gray-300' }}">
                        {!! $opt->option !!}
                    </button>
                @endforeach
            </div>

            {{-- ================= NEXT BUTTON ================= --}}
            @if($selectedOption)
                <button
                    wire:click="nextQuestion"
                    wire:loading.attr="disabled"
                    class="mt-6 w-full bg-green-600 text-white p-4 rounded-lg font-semibold shadow
                           hover:bg-green-700 transition disabled:opacity-60">
                    Selanjutnya
                </button>
            @endif

        </div>
    </div>


    {{-- ================= POPUP SELESAI ================= --}}
    @if($showFinishPopup)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-xl text-center shadow-2xl">
                <h2 class="text-2xl font-bold mb-3 text-green-600">Tes Selesai!</h2>
                <p class="text-gray-700 text-lg mb-4">
                    Kamu telah menyelesaikan Tes SPM.
                </p>
                <button
                    onclick="window.location.href='/dashboard'"
                    class="w-full bg-blue-600 text-white p-3 rounded-lg font-semibold">
                    Kembali ke Dashboard
                </button>
            </div>
        </div>
    @endif


    {{-- ================= JAVASCRIPT TIMER ================= --}}
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
                document.getElementById('progressBar').style.width = percentage + '%';
            });

        });
    </script>


    {{-- ================= ANTI CHEAT ================= --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            let unloadProtectionEnabled = true;

            window.addEventListener("beforeunload", e => {
                if (!unloadProtectionEnabled) return;
                e.preventDefault();
                e.returnValue = "";
            });

            history.pushState(null, null, location.href);
            window.onpopstate = () => history.go(1);

            document.addEventListener("visibilitychange", () => {
                if (document.hidden) {
                    Livewire.dispatch('forceSubmit');
                }
            });
        });
    </script>

</div>
