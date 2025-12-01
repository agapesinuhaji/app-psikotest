<?php

namespace App\Filament\Resources\TypeQuestions\Pages;

use App\Filament\Resources\TypeQuestions\TypeQuestionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTypeQuestion extends ViewRecord
{
    protected static string $resource = TypeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
