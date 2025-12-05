<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ClientTest;

class ExamPage extends Component
{
    public $clientTest;

    public function mount()
    {
        $this->clientTest = ClientTest::firstOrCreate(
            ['user_id' => auth()->id()],
            []
        );

        // Set waktu mulai SPM jika belum diset
        if (!$this->clientTest->spm_start_at) {
            $this->clientTest->update([
                'spm_start_at' => now(),
            ]);
        }
    }

    public function finishExam()
    {
        $this->clientTest->update([
            'spm_end_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Ujian SPM selesai.');
    }

    public function render()
    {
        return view('livewire.exam-page');
    }
}
