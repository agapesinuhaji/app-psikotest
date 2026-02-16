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
    public int $currentIndex = 0;

    public ?int $selectedOption = null;

    public array $answers = []; // ⬅️ SIMPAN JAWABAN

    public $clientTest;

    public bool $showPopup = true;
    public bool $showFinishPopup = false;

    #[Locked]
    public int $duration;

    public function mount()
    {
        $user = auth()->user();
        $batch = Batch::findOrFail($user->batch_id);

        if ($user->is_active != 1 || $batch->status !== 'open') {
            abort(403, 'Anda belum memiliki akses ujian.');
        }

        $this->clientTest = ClientTest::firstOrCreate(
            ['user_id' => $user->id],
            ['spm_start_at' => now()]
        );

        $type = TypeQuestion::where('slug', 'spm')->firstOrFail();
        $this->duration = ((int) $type->duration) * 60;

        $this->questions = Question::with('options')
            ->where('type_question_id', $type->id)
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            abort(404, 'Soal SPM kosong!');
        }

        $this->restoreAnswer();
    }

    public function startTest()
    {
        $this->clientTest->update(['spm_start_at' => now()]);
        $this->showPopup = false;

        $this->dispatch('startTimer', duration: $this->duration);
        $this->dispatch('enterFullscreen');
    }

    public function selectOption(int $optionId)
    {
        $this->selectedOption = $optionId;

        $this->answers[$this->currentQuestion()->id] = $optionId;
    }

    public function nextQuestion()
    {
        $this->saveAnswer();

        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;
            $this->restoreAnswer();
            $this->updateProgress();
        } else {
            $this->finishTest();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->restoreAnswer();
            $this->updateProgress();
        }
    }

    private function saveAnswer()
    {
        if (!$this->selectedOption) return;

        $question = $this->currentQuestion();
        $option = Option::findOrFail($this->selectedOption);

        ClientQuestion::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'question_id' => $question->id,
            ],
            [
                'option_id' => $option->id,
                'score' => $option->value,
                'is_correct' => $option->value == 1,
                'is_active' => 1,
            ]
        );
    }

    private function restoreAnswer()
    {
        $this->selectedOption =
            $this->answers[$this->currentQuestion()->id] ?? null;
    }

    private function updateProgress()
    {
        $percentage = (($this->currentIndex + 1) / count($this->questions)) * 100;
        $this->dispatch('updateProgress', percentage: $percentage);
    }

    private function currentQuestion()
    {
        return $this->questions[$this->currentIndex];
    }

    public function finishTest()
    {
        $this->clientTest->update(['spm_end_at' => now()]);
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
            'question' => $this->currentQuestion(),
            'currentNumber' => $this->currentIndex + 1,
            'totalQuestions' => count($this->questions),
        ])->layout('layouts.app');
    }
}
