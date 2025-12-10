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

    #[Locked]
    public $duration = 3600; // contoh: 60 menit = 3600 detik

    public function mount()
    {
        $user = auth()->user();

        // Ambil batch berdasarkan batch_id dari user yang login
        $batch = Batch::findOrFail($user->batch_id);

        // Pastikan user aktif dan batch status open
        if ($user->is_active != 1 || $batch->status !== 'open') {
            abort(403, 'Anda belum memiliki akses ujian.');
        }

        $this->clientTest = ClientTest::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $type = TypeQuestion::where('slug', 'spm')->firstOrFail();
        $this->questions = Question::where('type_question_id', $type->id)
            ->with('options')
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            abort(404, "Soal SPM kosong!");
        }
    }



    public function startTest()
    {
        $clientTest = ClientTest::firstOrCreate(
            ['user_id' => auth()->id()],
            []
        );

        $clientTest->update([
            'spm_start_at' => now()
        ]);

        $this->showPopup = false;

        $this->dispatch('startTimer', duration: $this->duration);

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

        $isCorrect = $option->value == 1; // otomatis benar/salah

        ClientQuestion::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'question_id' => $current->id
            ],
            [
                'option_id' => $option->id,
                'score' => $option->value,   // 1 untuk benar, 0 untuk salah
                'is_correct' => $isCorrect,  // simpan kebenaran jawaban
                'is_active' => 1
            ]
        );

        $this->selectedOption = null;

        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;

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
            'showPopup' => $this->showPopup
        ])->layout('layouts.app');
    }
}
