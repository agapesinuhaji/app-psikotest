<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;
use App\Models\Option;
use App\Models\ClientQuestion;
use App\Models\ClientTest;
use App\Models\TypeQuestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;

class PapikostickTest extends Component
{
    public $questions;
    public $currentIndex = 0;
    public $selectedOption = null;
    public $clientTest;

    public $showPopup = true;
    public $showFinishPopup = false;

    
    #[Locked] 
    public $duration = 7200; // 120 menit = 7200 detik

    public function mount()
    {
        $userId = Auth::id();

        $this->clientTest = ClientTest::firstOrCreate(
            ['user_id' => $userId],
            ['papikostick_start_at' => now()]
        );

        $type = TypeQuestion::where('slug', 'papi-kostick')->firstOrFail();

        $this->questions = Question::where('type_question_id', $type->id)
            ->with('options')
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            abort(404, "Soal Papikostick kosong!");
        }
    }

    public function startTest()
    {
        // Ambil atau buat record client_tests milik user
        $clientTest = \App\Models\ClientTest::firstOrCreate(
            ['user_id' => auth()->id()],
            []
        );

        // Update waktu mulai Papikostick
        $clientTest->update([
            'papikostick_start_at' => now()
        ]);

        // Tutup popup
        $this->showPopup = false;

        // Jalankan timer
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

        ClientQuestion::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'question_id' => $current->id
            ],
            [
                'option_id' => $option->id,
                'score' => $option->value,
                'is_active' => 1
            ]
        );

        $this->selectedOption = null;

        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;

            // === 👉 Tambahkan ini! ===
            $percentage = (($this->currentIndex + 1) / count($this->questions)) * 100;
            $this->dispatch('updateProgress', percentage: $percentage);

        } else {
            $this->finishTest();
        }
    }


    public function finishTest()
    {
        $this->clientTest->update([
            'papikostick_end_at' => now()
        ]);

        $this->showFinishPopup = true;

        // Hapus proteksi beforeunload
        $this->dispatch('disableUnloadProtection');
        

        // Trigger redirect
        $this->dispatch('testFinished');
    }


    #[\Livewire\Attributes\On('forceSubmit')]
    public function forceSubmit()
    {
        $this->finishTest();
    }

    public function render()
    {
        return view('livewire.papikostick-test', [
            'question' => $this->questions[$this->currentIndex],
            'currentIndex' => $this->currentIndex,
            'questions' => $this->questions,
            'selectedOption' => $this->selectedOption,
            'showPopup' => $this->showPopup
        ])->layout('layouts.app');
    }
}
