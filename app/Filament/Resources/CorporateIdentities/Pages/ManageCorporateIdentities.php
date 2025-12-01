<?php

namespace App\Filament\Resources\CorporateIdentities\Pages;

use App\Filament\Resources\CorporateIdentities\CorporateIdentityResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCorporateIdentities extends ManageRecords
{
    protected static string $resource = CorporateIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
