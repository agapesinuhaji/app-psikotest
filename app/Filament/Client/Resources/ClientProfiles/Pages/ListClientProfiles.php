<?php

namespace App\Filament\Client\Resources\ClientProfiles\Pages;

use App\Filament\Client\Resources\ClientProfiles\ClientProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientProfiles extends ListRecords
{
    protected static string $resource = ClientProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
