<?php

namespace App\Filament\Resources\TypeQuestions\Pages;

use App\Filament\Resources\TypeQuestions\TypeQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTypeQuestion extends EditRecord
{
    protected static string $resource = TypeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Redirect ke halaman view setelah record diupdate
        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
    }
    
}
