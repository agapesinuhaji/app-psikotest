<?php

namespace App\Services;

use App\Models\ClientQuestion;
use App\Models\PapikostickResult; // pastikan model ini ada (atau ganti nama sesuai modelmu)
use App\Models\User;
use Illuminate\Support\Arr;

class PapikostickResultService
{
    /**
     * Proses semua perhitungan Papikostick untuk satu user
     *
     * @param int $userId
     * @return array hasil ringkasan
     */
    public static function processUser(int $userId): array
    {
        $answers = ClientQuestion::where('user_id', $userId)
            ->whereNotNull('option_id') // asumsi option_id berisi jawaban
            ->with(['question', 'option']) // pastikan relasi ada
            ->get();

        // Map soal => expected letter (A or B) untuk setiap aspek (dari definisimu)
        $map = self::mappingQuestions();

        // Hitung skor per aspek berdasarkan kecocokan jawaban user dengan expected letter
        $scores = [];
        foreach ($map as $aspect => $questionNumbers) {
            $scores[$aspect] = 0;
            foreach ($questionNumbers as $num => $expectedLetter) {
                // cari answer sesuai nomor soal
                $cq = $answers->first(function ($a) use ($num) {
                    $q = $a->question;
                    $no = $q->number ?? $q->order ?? $q->id ?? null;
                    return (int) $no === (int) $num;
                });

                if (! $cq) {
                    continue; // soal tidak ditemukan / belum dijawab
                }

                $ansLetter = self::getAnswerLetter($cq);
                if (! $ansLetter) continue;

                if (strtoupper($ansLetter) === strtoupper($expectedLetter)) {
                    $scores[$aspect]++;
                }
            }
        }

        // Sekarang hasil per aspek (G, C, N, A, W, S, K, Z, C2 (managing change))
        // Kita ambil skor yang namanya sesuai mappingQuestions keys
        // Nama keys: G, C, N, A, W, S, K, Z, C2
        $g = $scores['G'] ?? 0;   // Orientasi Hasil (G)
        $c = $scores['C'] ?? 0;   // Fleksibilitas (C)
        $n = $scores['N'] ?? 0;   // Sistematika Kerja (N)
        $a = $scores['A'] ?? 0;   // Motivasi Berprestasi (A)
        $w = $scores['W'] ?? 0;   // Kerjasama (W)
        $s = $scores['S'] ?? 0;   // Keterampilan Interpersonal (S)
        $k = $scores['K'] ?? 0;   // Stabilitas Emosi (K)
        $z = $scores['Z'] ?? 0;   // Pengembangan Diri (Z)
        $c2 = $scores['C2'] ?? 0; // Mengelola Perubahan (C2) — note: 'C' sudah dipakai fleksibilitas

        // 1) Sikap & Cara Kerja = total G + C + N
        $sikap_total = $g + $c + $n;
        $sikap_conclusion = self::concludeSikapCaraKerja($sikap_total);

        // 2) Kepribadian = total A + W + S + K
        $kepribadian_total = $a + $w + $s + $k;
        $kepribadian_conclusion = self::concludeKepribadian($kepribadian_total);

        // 3) Kemampuan Belajar = total Z + C2
        $belajar_total = $z + $c2;
        $belajar_conclusion = self::concludeKemampuanBelajar($belajar_total);

        // 4) Kesimpulan akhir: LAYAK / TIDAK VALID / TIDAK LAYAK
        $passes = [
            'sikap' => self::isPassedSikap($sikap_total),
            'kepribadian' => self::isPassedKepribadian($kepribadian_total),
            'belajar' => self::isPassedBelajar($belajar_total),
        ];

        $notPassedCount = collect($passes)->filter(fn($v) => ! $v)->count();

        if ($passes['sikap'] && $passes['kepribadian'] && $passes['belajar']) {
            $finalConclusion = 'LAYAK';
        } elseif ($notPassedCount >= 2) {
            $finalConclusion = 'TIDAK VALID';
        } else {
            $finalConclusion = 'TIDAK LAYAK';
        }

        // Simpan ke tabel papikostick_results (sesuaikan model & kolommu)
        PapikostickResult::updateOrCreate(
            ['user_id' => $userId],
            [
                'result_orientation'       => $g,
                'flexibility'              => $c,
                'systematic_work'          => $n,
                'achievement_motivation'   => $a,
                'cooperation'              => $w,
                'interpersonal_skills'     => $s,
                'emotional_stability'      => $k,
                'self_development'         => $z,
                'managing_change'          => $c2,
                'g_c_n_score'              => $sikap_total,
                'g_c_n_conclusion'         => $sikap_conclusion,
                'a_w_s_k_score'            => $kepribadian_total,
                'a_w_s_k_conclusion'       => $kepribadian_conclusion,
                'z_c_score'                => $belajar_total,
                'z_c_conclusion'           => $belajar_conclusion,
                'final_conclusion'         => $finalConclusion,
                'start_time'               => now(), // ubah bila ada sumber waktu yg tepat
            ]
        );

        // Kembalikan ringkasan (berguna untuk log)
        return [
            'scores' => $scores,
            'sikap_total' => $sikap_total,
            'sikap_conclusion' => $sikap_conclusion,
            'kepribadian_total' => $kepribadian_total,
            'kepribadian_conclusion' => $kepribadian_conclusion,
            'belajar_total' => $belajar_total,
            'belajar_conclusion' => $belajar_conclusion,
            'final_conclusion' => $finalConclusion,
            'passes' => $passes,
        ];
    }

    /**
     * Mapping soal -> expected letter
     *
     * Format:
     * 'G' => [ 1 => 'A', 11 => 'A', ... ]
     */
    private static function mappingQuestions(): array
    {
        return [
            // 6. Orientasi Hasil = G
            'G' => array_fill_keys([1,11,21,31,41,51,61,71,81], 'A'),

            // 7. Fleksibilitas = C (gunakan key C_forFlex)
            'C' => array_fill_keys([11,22,33,44,55,66,77,88,89], 'A'),

            // 8. Sistematika Kerja = N
            'N' => array_fill_keys([2,13,24,35,46,57,68,79,90], 'B'),

            // 9. Motivasi Berprestasi = A
            'A' => [
                2 => 'A', 3 => 'B', 14 => 'B', 25 => 'B', 36 => 'B',
                47 => 'B', 58 => 'B', 69 => 'B', 80 => 'B'
            ],

            // 10. Kerjasama = W
            'W' => array_fill_keys([10,20,30,40,50,60,70,80,90], 'A'),

            // 11. Keterampilan Interpersonal = S
            'S' => [
                52 => 'B', 56 => 'A', 61 => 'B', 63 => 'B', 66 => 'A',
                74 => 'B', 76 => 'A', 85 => 'B', 86 => 'A'
            ],

            // 12. Stabilitas Emosi = K
            'K' => [
                8 => 'B', 9 => 'A', 18 => 'B', 20 => 'A', 28 => 'B',
                38 => 'B', 48 => 'B', 58 => 'B', 68 => 'B'
            ],

            // 13. Pengembangan Diri = Z
            'Z' => [
                7 => 'A', 8 => 'B', 17 => 'A', 19 => 'B', 27 => 'A',
                30 => 'B', 37 => 'A', 47 => 'A', 57 => 'A'
            ],

            // 14. Mengelola Perubahan = C2 (gunakan key C2 to avoid colliding 'C' above)
            'C2' => array_fill_keys([11,22,33,44,55,66,77,88,89], 'A'),
        ];
    }

    /**
     * Ambil huruf jawaban user dari ClientQuestion instance
     * (robust: cek beberapa properti relasi option)
     */
    private static function getAnswerLetter($clientQuestion)
    {
        // Jika ada relasi option dan punya code/label
        $opt = $clientQuestion->option ?? null;
        if ($opt) {
            $letter = $opt->code ?? $opt->label ?? $opt->value ?? null;
            if ($letter) return strtoupper(substr(trim($letter), 0, 1));
        }

        // Jika ada field jawaban langsung (misal answer)
        if (isset($clientQuestion->answer) && $clientQuestion->answer) {
            return strtoupper(substr(trim($clientQuestion->answer), 0, 1));
        }

        // Jika kolom option_id menyimpan A/B (tidak umum), pakai itu
        if (isset($clientQuestion->option_id) && is_string($clientQuestion->option_id)) {
            return strtoupper(substr(trim($clientQuestion->option_id), 0, 1));
        }

        // fallback: tidak diketahui
        return null;
    }

    /**
     * Kesimpulan Sikap & Cara Kerja (berdasarkan tabel yang kamu berikan)
     */
    private static function concludeSikapCaraKerja(int $score): string
    {
        return match (true) {
            $score <= 3 => 'Sdr X tidak memiliki sikap dan cara kerja yang memadai dalam menyelesaikan tugas-tugasnya.',
            $score <= 6 => 'Sdr X kurang memiliki sikap dan cara kerja yang memadai dalam menyelesaikan tugas-tugasnya.',
            $score <= 12 => 'Sdr X memiliki sikap dan cara kerja yang cukup memadai dalam menyelesaikan tugas-tugasnya.',
            $score <= 21 => 'Sdr X memiliki sikap dan cara kerja yang baik dalam menyelesaikan tugas-tugasnya.',
            default => 'Sdr X memiliki sikap dan cara kerja yang sangat baik dalam menyelesaikan tugas-tugasnya.',
        };
    }

    private static function concludeKepribadian(int $score): string
    {
        return match (true) {
            $score <= 4 => 'Sdr X tidak memiliki kepribadian yang memadai untuk beradaptasi dan membawa diri dalam menyesuaikan diri di lingkungannya.',
            $score <= 8 => 'Sdr X kurang memiliki kepribadian yang baik untuk beradaptasi dan membawa diri dalam menyesuaikan diri di lingkungannya.',
            $score <= 16 => 'Sdr X memiliki kepribadian yang cukup memadai untuk beradaptasi dan membawa diri dalam menyesuaikan diri di lingkungannya.',
            $score <= 28 => 'Sdr X memiliki kepribadian yang baik untuk beradaptasi dan membawa diri dalam menyesuaikan diri di lingkungannya.',
            default => 'Sdr X memiliki kepribadian yang sangat baik untuk beradaptasi dan membawa diri dalam menyesuaikan diri di lingkungannya.',
        };
    }

    private static function concludeKemampuanBelajar(int $score): string
    {
        return match (true) {
            $score <= 2 => 'Sdr X tidak memiliki kemampuan yang memadai dalam mempelajari hal baru dan mengelola perubahan.',
            $score <= 4 => 'Sdr X kurang memiliki memiliki kemampuan yang baik dalam mempelajari hal baru dan mengelola perubahan.',
            $score <= 8 => 'Sdr X memiliki kemampuan yang cukup memadai dalam mempelajari hal baru dan mengelola perubahan.',
            $score <= 14 => 'Sdr X memiliki kemampuan yang baik dalam mempelajari hal baru dan mengelola perubahan.',
            default => 'Sdr X memiliki kemampuan yang sangat baik dalam mempelajari hal baru dan mengelola perubahan.',
        };
    }

    // fungsi-fungsi pass criteria (bisa diubah sesuai kebijakan)
    private static function isPassedSikap(int $score): bool { return $score >= 7; }
    private static function isPassedKepribadian(int $score): bool { return $score >= 9; }
    private static function isPassedBelajar(int $score): bool { return $score >= 6; }
}
