<?php

namespace App\Services;

use App\Models\User;
use App\Models\Batch;
use App\Models\Result;
use App\Models\SPMResult;
use App\Models\CorporateIdentity;
use App\Models\PapikostickResult;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Carbon\Carbon;

class ResultPdfService
{
    /**
     * =====================================================
     * GENERATE PDF
     * =====================================================
     */
    public static function generate(User $user): string
    {
        // Path template DOCX
        $templatePath = storage_path('app/templates/hasil-psikotes.docx');

        // Temp folder untuk output
        $outputDir = storage_path('app/temp');
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        // Nama file PDF
        $fileName   = 'hasil-psikotes-' . Str::slug($user->name) . '.pdf';
        $outputPath = $outputDir . '/' . $fileName;

        // ===============================
        // Load template DOCX
        // ===============================
        $template = new TemplateProcessor($templatePath);

        // -------------------------------
        // Corporate Identity
        // -------------------------------
        $corp = CorporateIdentity::first();
        $template->setValues([
            'CORPORATE_IDENTITY_PSIKOLOG'   => $corp->psikolog ?? '-',
            'CORPORATE_IDENTITY_NAME'       => $corp->name ?? '-',
            'CORPORATE_IDENTITY_ADDRESS'    => $corp->address ?? '-',
            'CORPORATE_IDENTITY_STRK_SIK'   => $corp->strk_sik ?? '-',
            'CORPORATE_IDENTITY_SIPP_SIPPK' => $corp->sipp_sippk ?? '-',
        ]);

        // -------------------------------
        // Data Peserta
        // -------------------------------
        $batch = Batch::find($user->batch_id);
        $template->setValues([
            'USER_NAME' => $user->name,
            'USER_USERNAME' => $user->username,
            'USER_AGE' => $user->age,
            'USER_GENDER' => match ($user->gender) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => '-',
            },
            'USER_LAST_EDUCATION' => $user->last_education,
            'USER_PLACE_OF_BIRTH' => $user->place_of_birth,
            'USER_DATE_OF_BIRTH' => $user->date_of_birth
                ? Carbon::parse($user->date_of_birth)->translatedFormat('d F Y')
                : '-',
            'BATCH_START_AT' => $batch?->start_time
                ? Carbon::parse($batch->start_time)->translatedFormat('d F Y')
                : '-',
            'BATCH_END_AT' => $batch?->end_time
                ? Carbon::parse($batch->end_time)->translatedFormat('d F Y')
                : '-',
        ]);

        // -------------------------------
        // SPM Result
        // -------------------------------
        $spm = SPMResult::where('user_id', $user->id)->first();
        $spmAspects = [
            'LOGIKA_BERPIKIR'    => $spm?->logical_thinking,
            'DAYA_ANALISA'       => $spm?->analytical_power,
            'KEMAMPUAN_NUMERIK'  => $spm?->numerical_ability,
            'KEMAMPUAN_VERBAL'   => $spm?->verbal_ability,
        ];

        foreach ($spmAspects as $prefix => $score) {
            if ($score === null) continue;
            $code = self::mapScoreToCode((int) $score);
            foreach (['R','K','C','B','T'] as $opt) {
                $template->setValue("{$prefix}_{$opt}", $code === $opt ? '✘' : '');
            }
        }
        $template->setValue('SPM_RESULT_CATEGORY', $spm?->category ?? '-');
        $template->setValue('SPM_RESULT_GRADE', $spm?->grade ?? '-');

        // -------------------------------
        // Papikostick Result
        // -------------------------------
        $papi = PapikostickResult::where('user_id', $user->id)->first();

        $workAspects = [
            'ORIENTASI_HASIL' => [$papi?->result_orientation, 'mapNormal'],
            'FLEKSIBILITAS' => [$papi?->flexibility, 'mapReverse'],
            'SISTEMATIKA_KERJA' => [$papi?->systematic_work, 'mapNormal'],
        ];
        $personalityAspects = [
            'MOTIVASI_BERPRESTASI' => [$papi?->achievement_motivation, 'mapNormal'],
            'KERJASAMA' => [$papi?->cooperation, 'mapNormal'],
            'KETERAMPILAN_INTERPERSONAL' => [$papi?->interpersonal_skills, 'mapNormal'],
            'STABILITAS_EMOSI' => [$papi?->emotional_stability, 'mapReverse'],
        ];
        $learningAspects = [
            'PENGEMBANGAN_DIRI' => [$papi?->self_development, 'mapNormal'],
            'MENGELOLA_PERUBAHAN' => [$papi?->managing_change, 'mapNormal'],
        ];

        foreach ([$workAspects, $personalityAspects, $learningAspects] as $group) {
            foreach ($group as $prefix => [$score, $mapper]) {
                if ($score === null) continue;
                $code = self::$mapper((int) $score);
                foreach (['R','K','C','B','T'] as $opt) {
                    $template->setValue("{$prefix}_{$opt}", $code === $opt ? '✘' : '');
                }
            }
        }

        $template->setValue('PAPIKOSTICK_RESULT_G_N_C_SCORE', $papi?->g_c_n_conclusion ?? '-');
        $template->setValue('PAPIKOSTICK_RESULT_A_W_S_K_CONCLUSION', $papi?->a_w_s_k_conclusion ?? '-');
        $template->setValue('PAPIKOSTICK_RESULT_Z_C_CONCLUSION', $papi?->z_c_conclusion ?? '-');

        $result = Result::where('user_id', $user->id)->first();
        $template->setValue('RESULT_CONCLUSION', $result?->conclusion ?? '-');

        // ===============================
        // Save temporary DOCX
        // ===============================
        $tempDocxPath = $outputDir . '/temp-' . Str::slug($user->name) . '.docx';
        $template->saveAs($tempDocxPath);

        // ===============================
        // Convert DOCX → PDF via mPDF
        // ===============================
        Settings::setPdfRendererName(Settings::PDF_RENDERER_MPDF);
        Settings::setPdfRendererPath(base_path('vendor/mpdf/mpdf'));

        $phpWord = IOFactory::load($tempDocxPath);
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($outputPath);

        // Hapus file DOCX sementara
        @unlink($tempDocxPath);

        return $outputPath;
    }

    // =====================================================
    // Mapping helper (SPM)
    // =====================================================
    private static function mapScoreToCode(int $score): string
    {
        return match (true) {
            $score <= 3  => 'R',
            $score <= 5  => 'K',
            $score <= 9  => 'C',
            $score <= 11 => 'B',
            default      => 'T',
        };
    }

    private static function mapNormal(int $score): string
    {
        return match (true) {
            $score <= 1 => 'R',
            $score === 2 => 'K',
            $score <= 4 => 'C',
            $score <= 7 => 'B',
            default     => 'T',
        };
    }

    private static function mapReverse(int $score): string
    {
        return match (true) {
            $score <= 1 => 'T',
            $score === 2 => 'B',
            $score <= 4 => 'C',
            $score <= 7 => 'K',
            default     => 'R',
        };
    }
}