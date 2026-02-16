
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


<style>
/* ================= PERTANYAAN ================= */

.question-content {
    text-align: center;
}

.question-content p {
    margin: 0;
}

.question-content img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;

    /* Batasi tinggi agar tidak terlalu besar */
    max-height: 220px;
}

/* Tablet */
@media (min-width: 640px) {
    .question-content img {
        max-height: 260px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .question-content img {
        max-height: 320px;
    }
}


/* ================= OPSI ================= */

.option-content {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-content p {
    margin: 0;
}

.option-content img {
    max-width: 85%;
    max-height: 85%;
    object-fit: contain;
    display: block;
}

/* Supaya button tidak membesar aneh */
.option-button {
    width: 100%;
    aspect-ratio: 1 / 1; /* bikin kotak */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ================= GRID RESPONSIVE MANUAL ================= */

.option-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Mobile 2 */
    gap: 12px;
}

/* Tablet */
@media (min-width: 768px) {
    .option-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .option-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* ================= BUTTON STYLE ================= */

.option-button {
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    transition: all 0.2s ease;
}

.option-button:hover {
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.option-button.selected {
    background: #2563eb;
    border-color: #1d4ed8;
}

/* ================= IMAGE CONTROL ================= */

.option-content {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-content img {
    max-width: 85%;
    max-height: 85%;
    object-fit: contain;
}

</style>


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

        <div class="max-w-5xl mx-auto p-4 bg-white shadow-xl rounded-xl">

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

            {{-- ================= NOMOR SOAL ================= --}}
            <div class="flex justify-between items-center mb-3">

                <div class="text-sm sm:text-base font-semibold text-gray-600">
                    Soal {{ $currentIndex + 1 }} dari {{ count($questions) }}
                </div>

                <div class="text-xs sm:text-sm text-gray-500">
                    {{ round((($currentIndex + 1) / count($questions)) * 100) }}%
                </div>

            </div>

            {{-- ================= PERTANYAAN ================= --}}
            <div class="question-content text-center mb-4">
                {!! $question->question !!}
            </div>


            {{-- LOADING KHUSUS AREA SOAL --}}
            <div wire:loading wire:target="nextQuestion" class="py-10 text-center">
                <p class="text-gray-500 italic">Memuat soal & gambar...</p>
            </div>

            {{-- ================= OPSI ================= --}}
            <div
                wire:loading.remove
                wire:target="nextQuestion"
                class="option-grid">

                @foreach($question->options as $opt)
                    <button
                        wire:click="selectOption({{ $opt->id }})"
                        class="
                            option-button
                            {{ $selectedOption == $opt->id
                                ? 'selected'
                                : '' }}
                        ">

                        <div class="option-content">
                            {!! $opt->option !!}
                        </div>

                    </button>
                @endforeach
            </div>





            {{-- ================= NAVIGATION BUTTON ================= --}}
            <div class="mt-6 flex justify-between gap-3">

                {{-- BUTTON KEMBALI --}}
                <button
                    wire:click="previousQuestion"
                    wire:loading.attr="disabled"
                    @if($currentIndex === 0) disabled @endif
                    class="
                        w-1/2
                        bg-gray-500
                        text-white
                        p-4
                        rounded-lg
                        font-semibold
                        shadow
                        hover:bg-gray-600
                        transition
                        disabled:opacity-40
                        disabled:cursor-not-allowed
                    ">
                    Kembali
                </button>

                {{-- BUTTON SELANJUTNYA --}}
                <button
                    wire:click="nextQuestion"
                    wire:loading.attr="disabled"
                    class="
                        w-1/2
                        bg-green-600
                        text-white
                        p-4
                        rounded-lg
                        font-semibold
                        shadow
                        hover:bg-green-700
                        transition
                        disabled:opacity-60
                    "
                    @if(!$selectedOption) disabled @endif
                >
                    {{ $currentIndex == $totalQuestions - 1 ? 'Selesai' : 'Selanjutnya' }}
                </button>

            </div>


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
