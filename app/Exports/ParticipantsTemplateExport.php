<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithSampleData;

class ParticipantsTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'place_of_birth',
            'date_of_birth',
            'gender',
            'last_education',
            'phone',
        ];
    }
}