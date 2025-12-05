<?php

namespace App\Http\Controllers;

use App\Models\ClientTest;
use Illuminate\Http\Request;
use App\Models\Question;

class SPMController extends Controller
{
    public function index()
    {
        $clientTest = ClientTest::where('user_id', auth()->id())->first();

        // Jika belum mulai, tampilkan popup modal
        $mustShowModal = is_null($clientTest->spm_start_at);

        return view('tests.spm', compact('clientTest', 'mustShowModal'));
    }

    public function begin(Request $request)
    {
        $clientTest = ClientTest::where('user_id', auth()->id())->first();

        if (!$clientTest->spm_start_at) {
            $clientTest->update([
                'spm_start_at' => now(),
                'spm_end_at'   => now()->addMinutes(30), // durasi 30 menit
            ]);
        }

        return response()->json(['status' => 'started']);
    }

    public function submit(Request $request)
    {
        // Simpan seluruh jawaban

        return redirect('/dashboard')->with('success', 'Ujian selesai.');
    }
}
