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

    private static function mapGrade(int $score, int $age): array
    {
        // ===============================
        // 1. TENTUKAN KELOMPOK USIA
        // ===============================
        $ageGroup = match (true) {
            $age >= 14 && $age <= 20 => '14-20',
            $age >= 21 && $age <= 25 => '21-25',
            $age >= 26 && $age <= 30 => '26-30',
            $age >= 31 && $age <= 35 => '31-35',
            $age >= 36 && $age <= 40 => '36-40',
            default => null,
        };

        if (! $ageGroup) {
            return [
                'grade' => '-',
                'category' => 'Usia tidak valid',
            ];
        }

        // ===============================
        // 2. NORMA SESUAI TABEL
        // ===============================
        $norms = [

            '14-20' => [
                ['grade'=>'I',   'min'=>53, 'max'=>60, 'category'=>'Superior'],
                ['grade'=>'II+', 'min'=>50, 'max'=>52, 'category'=>'Di atas rata-rata'],
                ['grade'=>'II',  'min'=>45, 'max'=>49, 'category'=>'Sedikit di atas rata-rata'],
                ['grade'=>'III+','min'=>40, 'max'=>44, 'category'=>'Rata-rata atas'],
                ['grade'=>'III', 'min'=>39, 'max'=>39, 'category'=>'Rata-rata'],
                ['grade'=>'III-','min'=>32, 'max'=>38, 'category'=>'Rata-rata bawah'],
                ['grade'=>'IV',  'min'=>24, 'max'=>31, 'category'=>'Sedikit di bawah rata-rata'],
                ['grade'=>'IV-', 'min'=>17, 'max'=>23, 'category'=>'Rata-rata'],
                ['grade'=>'V',   'min'=>0,  'max'=>16, 'category'=>'Di bawah rata-rata'],
            ],

            '21-25' => [
                ['grade'=>'I',   'min'=>55, 'max'=>60, 'category'=>'Superior'],
                ['grade'=>'II+', 'min'=>52, 'max'=>54, 'category'=>'Di atas rata-rata'],
                ['grade'=>'II',  'min'=>47, 'max'=>51, 'category'=>'Sedikit di atas rata-rata'],
                ['grade'=>'III+','min'=>43, 'max'=>46, 'category'=>'Rata-rata atas'],
                ['grade'=>'III', 'min'=>42, 'max'=>42, 'category'=>'Rata-rata'],
                ['grade'=>'III-','min'=>25, 'max'=>41, 'category'=>'Rata-rata bawah'],
                ['grade'=>'IV',  'min'=>23, 'max'=>24, 'category'=>'Sedikit di bawah rata-rata'],
                ['grade'=>'IV-', 'min'=>14, 'max'=>22, 'category'=>'Rata-rata'],
                ['grade'=>'V',   'min'=>0,  'max'=>13, 'category'=>'Di bawah rata-rata'],
            ],

            '26-30' => [
                ['grade'=>'I',   'min'=>52, 'max'=>60, 'category'=>'Superior'],
                ['grade'=>'II+', 'min'=>50, 'max'=>51, 'category'=>'Di atas rata-rata'],
                ['grade'=>'II',  'min'=>46, 'max'=>49, 'category'=>'Sedikit di atas rata-rata'],
                ['grade'=>'III+','min'=>43, 'max'=>45, 'category'=>'Rata-rata atas'],
                ['grade'=>'III', 'min'=>42, 'max'=>42, 'category'=>'Rata-rata'],
                ['grade'=>'III-','min'=>35, 'max'=>41, 'category'=>'Rata-rata bawah'],
                ['grade'=>'IV',  'min'=>25, 'max'=>34, 'category'=>'Sedikit di bawah rata-rata'],
                ['grade'=>'IV-', 'min'=>20, 'max'=>24, 'category'=>'Rata-rata'],
                ['grade'=>'V',   'min'=>0,  'max'=>19, 'category'=>'Di bawah rata-rata'],
            ],

            '31-35' => [
                ['grade'=>'I',   'min'=>53, 'max'=>60, 'category'=>'Superior'],
                ['grade'=>'II+', 'min'=>51, 'max'=>52, 'category'=>'Di atas rata-rata'],
                ['grade'=>'II',  'min'=>47, 'max'=>50, 'category'=>'Sedikit di atas rata-rata'],
                ['grade'=>'III+','min'=>43, 'max'=>46, 'category'=>'Rata-rata atas'],
                ['grade'=>'III', 'min'=>42, 'max'=>42, 'category'=>'Rata-rata'],
                ['grade'=>'III-','min'=>38, 'max'=>41, 'category'=>'Rata-rata bawah'],
                ['grade'=>'IV',  'min'=>31, 'max'=>37, 'category'=>'Sedikit di bawah rata-rata'],
                ['grade'=>'IV-', 'min'=>26, 'max'=>30, 'category'=>'Rata-rata'],
                ['grade'=>'V',   'min'=>0,  'max'=>25, 'category'=>'Di bawah rata-rata'],
            ],

            '36-40' => [
                ['grade'=>'I',   'min'=>54, 'max'=>60, 'category'=>'Superior'],
                ['grade'=>'II+', 'min'=>53, 'max'=>53, 'category'=>'Di atas rata-rata'],
                ['grade'=>'II',  'min'=>47, 'max'=>51, 'category'=>'Sedikit di atas rata-rata'],
                ['grade'=>'III+','min'=>42, 'max'=>46, 'category'=>'Rata-rata atas'],
                ['grade'=>'III', 'min'=>41, 'max'=>41, 'category'=>'Rata-rata'],
                ['grade'=>'III-','min'=>33, 'max'=>40, 'category'=>'Rata-rata bawah'],
                ['grade'=>'IV',  'min'=>26, 'max'=>32, 'category'=>'Sedikit di bawah rata-rata'],
                ['grade'=>'IV-', 'min'=>15, 'max'=>25, 'category'=>'Rata-rata'],
                ['grade'=>'V',   'min'=>0,  'max'=>14, 'category'=>'Di bawah rata-rata'],
            ],
        ];

        // ===============================
        // 3. COCOKKAN SKOR
        // ===============================
        foreach ($norms[$ageGroup] as $row) {
            if ($score >= $row['min'] && $score <= $row['max']) {
                return [
                    'grade'    => $row['grade'],
                    'category' => $row['category'],
                ];
            }
        }

        return [
            'grade' => '-',
            'category' => 'Tidak terklasifikasi',
        ];
    }

}
