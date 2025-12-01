<?php

namespace App\Filament\Resources\TypeQuestions\Pages;

use App\Filament\Resources\TypeQuestions\TypeQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTypeQuestion extends CreateRecord
{
    protected static string $resource = TypeQuestionResource::class;

    protected function afterCreate(): void
    {
        // Redirect ke halaman view setelah menyimpan
        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
    }
}
