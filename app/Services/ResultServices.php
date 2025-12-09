<?php

namespace App\Services;

use App\Models\User;
use App\Models\Question;
use App\Models\ClientQuestion;
use App\Models\SPMResult;
use Carbon\Carbon;

class ResultService
{
    public static function processUser($userId)
    {
        $user = User::findOrFail($userId);

        /*
        |--------------------------------------------------------------------------
        | HITUNG UMUR
        |--------------------------------------------------------------------------
        */
        $age = Carbon::parse($user->date_of_birth)->age;

        if ($age >= 14 && $age <= 20) $ageIndex = 0;
        elseif ($age >= 21 && $age <= 25) $ageIndex = 1;
        elseif ($age >= 26 && $age <= 30) $ageIndex = 2;
        elseif ($age >= 31 && $age <= 35) $ageIndex = 3;
        elseif ($age >= 36 && $age <= 40) $ageIndex = 4;
        else $ageIndex = null;

        /*
        |--------------------------------------------------------------------------
        | HITUNG SKOR PER GRUP SPM (A–E)
        |--------------------------------------------------------------------------
        */
        $groups = ['A', 'B', 'C', 'D', 'E'];
        $scores = array_fill_keys($groups, 0);

        foreach ($groups as $group) {
            $matched = ClientQuestion::where('user_id', $userId)
                ->where('question_code', $group)
                ->get();

            foreach ($matched as $cq) {
                $original = Question::find($cq->question_id);
                if ($original && $cq->answer === $original->key_answer) {
                    $scores[$group]++;
                }
            }
        }

        // TOTAL
        $totalSPM = array_sum($scores);

        /*
        |--------------------------------------------------------------------------
        | TABEL GRADE SPM
        |--------------------------------------------------------------------------
        */
        $gradeTable = [
            'I' => [
                'range' => [
                    [53, 60], [56, 60], [52, 60], [53, 60], [54, 60],
                ],
                'kategori' => 'Superior',
            ],
            'II+' => [
                'range' => [
                    [50, 52], [52, 54], [50, 51], [51, 52], [53, 53],
                ],
                'kategori' => 'Di atas rata-rata',
            ],
            'II' => [
                'range' => [
                    [45, 49], [47, 51], [46, 49], [47, 50], [47, 51],
                ],
                'kategori' => 'Sedikit di atas rata-rata',
            ],
            'III+' => [
                'range' => [
                    [40, 44], [43, 46], [43, 45], [43, 46], [42, 46],
                ],
                'kategori' => 'Rata-rata atas',
            ],
            'III' => [
                'range' => [
                    [39, 39], [42, 42], [41, 41], [42, 42], [41, 41],
                ],
                'kategori' => 'Rata-rata',
            ],
            'III-' => [
                'range' => [
                    [32, 38], [25, 41], [35, 41], [34, 41], [33, 40],
                ],
                'kategori' => 'Rata-rata bawah',
            ],
            'IV' => [
                'range' => [
                    [24, 31], [23, 24], [25, 31], [31, 37], [26, 32],
                ],
                'kategori' => 'Sedikit di bawah rata-rata',
            ],
            'IV-' => [
                'range' => [
                    [17, 23], [14, 22], [20, 24], [26, 30], [15, 25],
                ],
                'kategori' => 'Di bawah rata-rata',
            ],
            'V' => [
                'range' => [
                    [0, 16], [0, 13], [0, 19], [0, 25], [0, 14],
                ],
                'kategori' => 'Di bawah rata-rata',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | TENTUKAN GRADE DAN KATEGORI
        |--------------------------------------------------------------------------
        */
        $finalGrade = null;
        $finalKategori = null;

        if ($ageIndex !== null) {
            foreach ($gradeTable as $grade => $data) {
                [$min, $max] = $data['range'][$ageIndex];

                if ($totalSPM >= $min && $totalSPM <= $max) {
                    $finalGrade = $grade;
                    $finalKategori = $data['kategori'];
                    break;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KE TABEL spm_results
        |--------------------------------------------------------------------------
        */
        SPMResult::updateOrCreate(
            ['user_id' => $userId],
            [
                'spm_a' => $scores['A'],
                'spm_b' => $scores['B'],
                'spm_c' => $scores['C'],
                'spm_d' => $scores['D'],
                'spm_e' => $scores['E'],
                'spm_total' => $totalSPM,
                'spm_grade' => $finalGrade,
                'spm_kategori' => $finalKategori,
            ]
        );
    }
}
