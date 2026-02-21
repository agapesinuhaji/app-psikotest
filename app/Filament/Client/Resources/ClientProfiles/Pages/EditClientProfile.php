<?php

namespace App\Filament\Client\Resources\ClientProfiles\Pages;

use App\Filament\Client\Resources\ClientProfiles\ClientProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientProfile extends EditRecord
{
    protected static string $resource = ClientProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
