<?php

namespace App\Filament\Resources\TypeQuestions\Pages;

use App\Filament\Resources\TypeQuestions\TypeQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTypeQuestions extends ListRecords
{
    protected static string $resource = TypeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
