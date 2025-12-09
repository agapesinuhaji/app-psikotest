<?php

namespace App\Services;

use App\Models\ClientQuestion;
use App\Models\SPMResult;
use App\Models\User;

class SPMResultService
{
    public static function processUser($userId)
    {
        // Ambil semua jawaban user
        $answers = ClientQuestion::where('user_id', $userId)
            ->whereNotNull('score') // pastikan sudah disimpan
            ->with('question')       // relasi ke questions
            ->get();

        // Hitung nilai per kategori kode soal
        $logical = $answers->where('question.question_code', 'A')->sum('score');
        $analytical = $answers->where('question.question_code', 'B')->sum('score');
        $numerical = $answers->where('question.question_code', 'C')->sum('score');
        $verbal = $answers->where('question.question_code', 'D')->sum('score');
        $general = $answers->where('question.question_code', 'E')->sum('score');

        // Total keseluruhan
        $totalScore = $logical + $analytical + $numerical + $verbal + $general;

        // Ambil umur user
        $user = User::find($userId);
        $age = $user?->age ?? 0;

        // Dapatkan kategori & grade berdasarkan tabel
        $grading = self::mapGrade($totalScore, $age);

        // Simpan hasil
        SPMResult::updateOrCreate(
            ['user_id' => $userId],
            [
                'logical_thinking'   => $logical,
                'analytical_power'   => $analytical,
                'numerical_ability'  => $numerical,
                'verbal_ability'     => $verbal,
                'score'              => $totalScore,
                'category'           => $grading['category'],
                'grade'              => $grading['grade'],
                'is_finish'          => 1,
            ]
        );
    }

    private static function mapGrade($score, $age)
    {
        // Nanti kita isi sesuai tabelmu
        return [
            'category' => 'Cukup',
            'grade' => 'III',
        ];
    }
}
