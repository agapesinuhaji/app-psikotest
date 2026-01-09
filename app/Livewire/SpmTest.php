<?php

namespace App\Livewire;

use App\Models\Batch;
use App\Models\Option;
use Livewire\Component;
use App\Models\Question;
use App\Models\ClientTest;
use App\Models\TypeQuestion;
use App\Models\ClientQuestion;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Auth;

class SpmTest extends Component
{
    public $questions;
    public $currentIndex = 0;
    public $selectedOption = null;
    public $clientTest;

    public $showPopup = true;
    public $showFinishPopup = false;

    // ⏱️ DURASI DALAM DETIK (DB MENYIMPAN MENIT)
    #[Locked]
    public int $duration;

    public function mount()
    {
        $user = auth()->user();

        // Ambil batch user
        $batch = Batch::findOrFail($user->batch_id);

        // Validasi akses
        if ($user->is_active != 1 || $batch->status !== 'open') {
            abort(403, 'Anda belum memiliki akses ujian.');
        }

        // Ambil atau buat client test
        $this->clientTest = ClientTest::firstOrCreate(
            ['user_id' => $user->id],
            ['spm_start_at' => now()]
        );

        // Ambil type question SPM
        $type = TypeQuestion::where('slug', 'spm')->firstOrFail();

        // ✅ KONVERSI MENIT → DETIK
        // Contoh DB: 60 (menit) → 3600 (detik)
        $this->duration = ((int) $type->duration) * 60;

        // Ambil soal
        $this->questions = Question::where('type_question_id', $type->id)
            ->with('options')
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            abort(404, 'Soal SPM kosong!');
        }
    }

    public function startTest()
    {
        // Update waktu mulai
        $this->clientTest->update([
            'spm_start_at' => now()
        ]);

        // Tutup popup
        $this->showPopup = false;

        // ⏱️ Jalankan timer (DETIK)
        $this->dispatch('startTimer', duration: $this->duration);

        // Masuk fullscreen
        $this->dispatch('enterFullscreen');
    }

    public function selectOption($optionId)
    {
        $this->selectedOption = $optionId;
    }

    public function nextQuestion()
    {
        $current = $this->questions[$this->currentIndex];
        $option = Option::findOrFail($this->selectedOption);

        // 1 = benar, 0 = salah
        $isCorrect = $option->value == 1;

        ClientQuestion::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'question_id' => $current->id,
            ],
            [
                'option_id' => $option->id,
                'score' => $option->value,
                'is_correct' => $isCorrect,
                'is_active' => 1,
            ]
        );

        $this->selectedOption = null;

        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;

            // Update progress bar
            $percentage = (($this->currentIndex + 1) / count($this->questions)) * 100;
            $this->dispatch('updateProgress', percentage: $percentage);
        } else {
            $this->finishTest();
        }
    }

    public function finishTest()
    {
        $this->clientTest->update([
            'spm_end_at' => now()
        ]);

        $this->showFinishPopup = true;

        // Matikan proteksi unload
        $this->dispatch('disableUnloadProtection');

        $this->dispatch('testFinished');
    }

    #[\Livewire\Attributes\On('forceSubmit')]
    public function forceSubmit()
    {
        $this->finishTest();
    }

    public function render()
    {
        return view('livewire.spm-test', [
            'question' => $this->questions[$this->currentIndex],
            'currentIndex' => $this->currentIndex,
            'questions' => $this->questions,
            'selectedOption' => $this->selectedOption,
            'showPopup' => $this->showPopup,
        ])->layout('layouts.app');
    }
}
