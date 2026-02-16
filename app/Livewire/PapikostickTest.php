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

class PapikostickTest extends Component
{
    public $questions;
    public $currentIndex = 0;
    public $selectedOption = null;
    public $clientTest;

    public $showPopup = true;
    public $showFinishPopup = false;

    public $totalQuestions; // ✅ tambahan

    #[Locked]
    public int $duration;

    public function mount()
    {
        $user = auth()->user();

        $this->clientTest = ClientTest::firstOrCreate(
            ['user_id' => $user->id],
            ['papikostick_start_at' => now()]
        );

        $batch = Batch::findOrFail($user->batch_id);

        if ($user->is_active != 1 || $batch->status !== 'open') {
            abort(403, 'Anda belum memiliki akses ujian.');
        }

        $type = TypeQuestion::where('slug', 'papi-kostick')->firstOrFail();

        $this->duration = ((int) $type->duration) * 60;

        $this->questions = Question::where('type_question_id', $type->id)
            ->with('options')
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            abort(404, 'Soal Papikostick kosong!');
        }

        // ✅ total soal
        $this->totalQuestions = $this->questions->count();
    }

    public function startTest()
    {
        $this->clientTest->update([
            'papikostick_start_at' => now()
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

        ClientQuestion::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'question_id' => $current->id,
            ],
            [
                'option_id' => $option->id,
                'score' => $option->value,
                'is_active' => 1,
            ]
        );

        $this->selectedOption = null;

        if ($this->currentIndex < $this->totalQuestions - 1) {
            $this->currentIndex++;

            $percentage = (($this->currentIndex + 1) / $this->totalQuestions) * 100;
            $this->dispatch('updateProgress', percentage: $percentage);
        } else {
            $this->finishTest();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function finishTest()
    {
        $this->clientTest->update([
            'papikostick_end_at' => now()
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
        return view('livewire.papikostick-test', [
            'question' => $this->questions[$this->currentIndex],
            'currentIndex' => $this->currentIndex,
            'questions' => $this->questions,
            'selectedOption' => $this->selectedOption,
            'showPopup' => $this->showPopup,
            'totalQuestions' => $this->totalQuestions,
        ])->layout('layouts.app');
    }
}
