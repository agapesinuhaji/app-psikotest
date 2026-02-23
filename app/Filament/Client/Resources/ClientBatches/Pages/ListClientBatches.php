<?php

namespace App\Filament\Client\Resources\ClientBatches\Pages;

use App\Filament\Client\Resources\ClientBatches\ClientBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientBatches extends ListRecords
{
    protected static string $resource = ClientBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
