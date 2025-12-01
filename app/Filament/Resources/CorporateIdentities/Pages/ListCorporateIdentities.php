<?php

namespace App\Filament\Resources\CorporateIdentities\Pages;

use App\Filament\Resources\CorporateIdentities\CorporateIdentityResource;
use Filament\Resources\Pages\ListRecords;

class ListCorporateIdentities extends ListRecords
{
    protected static string $resource = CorporateIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
