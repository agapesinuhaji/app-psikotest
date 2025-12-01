<?php

namespace App\Filament\Resources\CorporateIdentities\Pages;

use App\Filament\Resources\CorporateIdentities\CorporateIdentityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCorporateIdentity extends ViewRecord
{
    protected static string $resource = CorporateIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
