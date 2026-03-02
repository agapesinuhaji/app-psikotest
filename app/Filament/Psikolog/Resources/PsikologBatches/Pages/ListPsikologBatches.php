<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches\Pages;

use App\Filament\Psikolog\Resources\PsikologBatches\PsikologBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPsikologBatches extends ListRecords
{
    protected static string $resource = PsikologBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
