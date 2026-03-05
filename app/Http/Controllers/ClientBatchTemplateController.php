<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;

class ClientBatchTemplateController extends Controller
{
    public function download()
    {
        $fileName = 'template-import-participants.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = new Writer();
        $writer->openToFile($filePath);

        // Header
        $header = Row::fromValues([
            'name',
            'email',
            'nik',
            'nama_ayah',
            'place_of_birth',
            'date_of_birth',
            'gender',
            'last_education',
            'phone',
        ]);

        $writer->addRow($header);

        // Contoh Data
        $writer->addRow(Row::fromValues([
            'Budi Santoso',
            'budi@mail.com',
            '0123456789012345',
            'Sutrisno',
            'Medan',
            '2000-01-01',
            'L',
            'S1',
            '08123456789',
        ]));

        $writer->addRow(Row::fromValues([
            'Siti Aisyah',
            'siti@mail.com',
            '0987654321098765',
            'Ahmad',
            'Binjai',
            '2001-05-10',
            'P',
            'SMA',
            '082345678901',
        ]));

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}