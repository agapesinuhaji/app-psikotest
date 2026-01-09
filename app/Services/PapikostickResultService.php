<?php

namespace App\Services;

use App\Models\ClientQuestion;
use App\Models\PapikostickResult;
use App\Models\Result;
use App\Models\User;



class PapikostickResultService
{
    public static function processUser(int $userId): array
    {
        /**
         * =====================================================
         * STEP 1
         * Ambil jawaban Papikostick (2 Option)
         * Nomor PAPI = urutan question_id terkecil
         * =====================================================
         */
        $answers = ClientQuestion::query()
            ->where('user_id', $userId)
            ->whereHas('question', fn ($q) => $q->where('question_code', '2 Option'))
            ->with(['question.options'])
            ->orderBy('question_id')
            ->get()
            ->values()
            ->map(function ($item, $index) {
                $item->papi_number = $index + 1;
                return $item;
            });

        /**
         * =====================================================
         * STEP 2
         * Mapping aspek
         * =====================================================
         */
        $mapping = self::mappingQuestions();

        /**
         * =====================================================
         * STEP 3
         * Inisialisasi skor aspek
         * =====================================================
         */
        $scores = [
            'G'  => 0,
            'C'  => 0,
            'N'  => 0,
            'A'  => 0,
            'W'  => 0,
            'S'  => 0,
            'K'  => 0,
            'Z'  => 0,
            'C2' => 0,
        ];

        /**
         * =====================================================
         * STEP 4
         * Hitung skor per aspek
         * =====================================================
         */
        foreach ($mapping as $aspect => $rules) {
            foreach ($rules as $papiNumber => $expected) {

                $answer = $answers->first(
                    fn ($a) => $a->papi_number === $papiNumber
                );

                if (! $answer) {
                    continue;
                }

                $options = $answer->question->options
                    ->sortBy('id')
                    ->values();

                if ($options->count() !== 2) {
                    continue;
                }

                $minOptionId = (int) $options[0]->id;
                $maxOptionId = (int) $options[1]->id;
                $chosen      = (int) $answer->option_id;

                if (
                    ($expected === 'MIN' && $chosen === $minOptionId) ||
                    ($expected === 'MAX' && $chosen === $maxOptionId)
                ) {
                    $scores[$aspect]++;
                }
            }
        }

        /**
         * =====================================================
         * STEP 5
         * HITUNG DIMENSI BESAR
         * =====================================================
         */
        $gcnScore  = $scores['G'] + $scores['C'] + $scores['N'];
        $awskScore = $scores['A'] + $scores['W'] + $scores['S'] + $scores['K'];
        $zcScore   = $scores['Z'] + $scores['C2'];

        $gcnConclusion  = self::concludeSikapCaraKerja($gcnScore);
        $awskConclusion = self::concludeKepribadian($awskScore);
        $zcConclusion   = self::concludeKemampuanBelajar($zcScore);




        /**
         * =====================================================
         * STEP 6
         * TENTUKAN KELAYAKAN (LAYAK / TIDAK LAYAK)
         * =====================================================
         */
        $kurangCount = 0;

        if ($gcnScore <= 11) {
            $kurangCount++;
        }

        if ($awskScore <= 13) {
            $kurangCount++;
        }

        if ($zcScore <= 4) {
            $kurangCount++;
        }

        $user = User::findOrFail($userId);
        $userName = $user->name;


        $finalConclusion = $kurangCount >= 2
            ? "Hasil tes Sdr {$userName} TIDAK VALID" 
            : "Berdasarkan pemeriksaan psikologis saat ini dengan memperhatikan seluruh gambaran aspek psikologis yang Sdri {$userName} miliki, rekomendasi untuk Sdri {$userName} adalah LAYAK untuk bekerja";


        /**
         * =====================================================
         * STEP 7
         * Simpan ke papikostick_results dan results
         * =====================================================
         */
        PapikostickResult::updateOrCreate(
            ['user_id' => $userId],
            [
                // skor aspek
                'result_orientation'     => $scores['G'],
                'flexibility'            => $scores['C'],
                'systematic_work'        => $scores['N'],
                'achievement_motivation' => $scores['A'],
                'cooperation'            => $scores['W'],
                'interpersonal_skills'   => $scores['S'],
                'emotional_stability'    => $scores['K'],
                'self_development'       => $scores['Z'],
                'managing_change'        => $scores['C2'],

                // dimensi besar
                'g_c_n_score'        => $gcnScore,
                'g_c_n_conclusion'   => $gcnConclusion,

                'a_w_s_k_score'      => $awskScore,
                'a_w_s_k_conclusion' => $awskConclusion,

                'z_c_score'          => $zcScore,
                'z_c_conclusion'     => $zcConclusion,
            ]
        );

        Result::updateOrCreate(
            ['user_id' => $userId],
            [
                'conclusion' => $finalConclusion,
            ]
        );

        return [
            'scores' => $scores,
            'gcn'  => [$gcnScore,  $gcnConclusion],
            'awsk' => [$awskScore, $awskConclusion],
            'zc'   => [$zcScore,   $zcConclusion],
        ];
    }


    
    



    /**
     * =====================================================
     * MAPPING PAPI → ASPEK
     * =====================================================
     */
    private static function mappingQuestions(): array
    {
        return [
            'G' => [1=>'MIN',11=>'MIN',21=>'MIN',31=>'MIN',41=>'MIN',51=>'MIN',61=>'MIN',71=>'MIN',81=>'MIN'],
            'C' => [11=>'MIN',22=>'MIN',33=>'MIN',44=>'MIN',55=>'MIN',66=>'MIN',77=>'MIN',88=>'MIN',89=>'MAX'],
            'N' => [2=>'MAX',13=>'MAX',24=>'MAX',35=>'MAX',46=>'MAX',57=>'MAX',68=>'MAX',79=>'MAX',89=>'MAX'],
            'A' => [2=>'MIN',3=>'MAX',14=>'MAX',25=>'MAX',36=>'MAX',47=>'MAX',58=>'MAX',69=>'MAX',80=>'MAX'],
            'W' => [5=>'MIN',16=>'MIN',27=>'MIN',38=>'MIN',49=>'MIN',60=>'MIN',71=>'MIN',82=>'MIN',93=>'MIN'],
            'S' => [52=>'MIN',56=>'MIN',61=>'MAX',63=>'MAX',66=>'MIN',74=>'MAX',76=>'MIN',85=>'MAX',86=>'MIN'],
            'K' => [8=>'MAX',9=>'MIN',18=>'MAX',20=>'MIN',28=>'MAX',38=>'MAX',48=>'MAX',58=>'MAX',68=>'MAX'],
            'Z' => [7=>'MIN',8=>'MAX',17=>'MIN',19=>'MAX',27=>'MIN',30=>'MAX',37=>'MIN',47=>'MIN',57=>'MIN'],
            'C2'=> [11=>'MIN',22=>'MIN',33=>'MIN',44=>'MIN',55=>'MIN',66=>'MIN',77=>'MIN',88=>'MIN',89=>'MAX'],
        ];
    }

    /**
     * =====================================================
     * CONCLUSION HELPERS
     * =====================================================
     */
    private static function concludeSikapCaraKerja(int $score): string
    {
        return match (true) {
            $score <= 10  => 'tidak memiliki sikap dan cara kerja yang memadai dalam menyelesaikan tugas-tugasnya.',
            $score == 11  => 'kurang memiliki sikap dan cara kerja yang memadai dalam menyelesaikan tugas-tugasnya.',
            $score <= 14 => 'memiliki sikap dan cara kerja yang cukup memadai dalam menyelesaikan tugas-tugasnya.',
            $score <= 17 => 'memiliki sikap dan cara kerja yang baik dalam menyelesaikan tugas-tugasnya.',
            $score <= 19 => 'memiliki sikap dan cara kerja yang sangat baik dalam menyelesaikan tugas-tugasnya.',
            default      => '-',
        };
    }

    private static function concludeKepribadian(int $score): string
    {
        return match (true) {
            $score <= 10  => 'tidak memiliki kepribadian yang memadai untuk beradaptasi dalam bekerja dan berinteraksi dengan orang-orang baru.',
            $score <= 13  => 'kurang memiliki kepribadian yang baik untuk beradaptasi dalam bekerja dan berinteraksi dengan orang-orang baru.',
            $score <= 16 => 'memiliki kepribadian yang cukup memadai untuk beradaptasi dalam bekerja dan berinteraksi dengan orang-orang baru.',
            $score <= 23 => 'memiliki kepribadian yang baik untuk beradaptasi dalam bekerja dan berinteraksi dengan orang-orang baru.',
            $score <= 28 => 'memiliki kepribadian yang sangat baik untuk beradaptasi dalam bekerja dan berinteraksi dengan orang-orang baru.',
            default      => '-',
        };
    }

    private static function concludeKemampuanBelajar(int $score): string
    {
        return match (true) {
            $score <= 2  => 'tidak memiliki kemampuan yang memadai dalam mempelajari hal baru dan menyesuaikan diri dengan situasi yang baru.',
            $score <= 4  => 'kurang memiliki memiliki kemampuan yang baik dalam mempelajari hal baru dan menyesuaikan diri dengan situasi yang baru.',
            $score <= 8  => 'memiliki kemampuan yang cukup memadai dalam mempelajari hal baru dan menyesuaikan diri dengan situasi yang baru.',
            $score <= 14 => 'memiliki kemampuan yang baik dalam mempelajari hal baru dan menyesuaikan diri dengan situasi yang baru.',
            $score <= 18 => 'memiliki kemampuan yang sangat baik dalam mempelajari hal baru dan menyesuaikan diri dengan situasi yang baru.',
            default      => '-',
        };
    }
}
