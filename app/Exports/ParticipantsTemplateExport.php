<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ParticipantsTemplateExport implements 
    WithHeadings, 
    WithColumnFormatting, 
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'nik',
            'nama_ayah',
            'place_of_birth',
            'date_of_birth',
            'gender',
            'last_education',
            'phone',
        ];
    }

    /**
     * Paksa format kolom tertentu
     */
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT, // NIK
            'I' => NumberFormat::FORMAT_TEXT, // PHONE
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD, // DATE
        ];
    }

    /**
     * 🔥 Extra force supaya Excel tidak ubah ke numeric
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode('@'); // NIK
        $sheet->getStyle('I:I')->getNumberFormat()->setFormatCode('@'); // PHONE

        return [];
    }

    /**
     * 🔥 Tambahkan 2 contoh data (L & P)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // =========================
                // CONTOH 1 - LAKI-LAKI (L)
                // =========================
                $sheet->setCellValue('A2', 'Budi Santoso');
                $sheet->setCellValue('B2', 'budi@mail.com');
                $sheet->setCellValueExplicit('C2', '0123456789012345', DataType::TYPE_STRING);
                $sheet->setCellValue('D2', 'Sutrisno');
                $sheet->setCellValue('E2', 'Medan');
                $sheet->setCellValue('F2', '2000-01-01');
                $sheet->setCellValue('G2', 'L');
                $sheet->setCellValue('H2', 'S1');
                $sheet->setCellValueExplicit('I2', '08123456789', DataType::TYPE_STRING);

                // =========================
                // CONTOH 2 - PEREMPUAN (P)
                // =========================
                $sheet->setCellValue('A3', 'Siti Aisyah');
                $sheet->setCellValue('B3', 'siti@mail.com');
                $sheet->setCellValueExplicit('C3', '0987654321098765', DataType::TYPE_STRING);
                $sheet->setCellValue('D3', 'Ahmad');
                $sheet->setCellValue('E3', 'Binjai');
                $sheet->setCellValue('F3', '2001-05-10');
                $sheet->setCellValue('G3', 'P');
                $sheet->setCellValue('H3', 'SMA');
                $sheet->setCellValueExplicit('I3', '082345678901', DataType::TYPE_STRING);
            },
        ];
    }
}