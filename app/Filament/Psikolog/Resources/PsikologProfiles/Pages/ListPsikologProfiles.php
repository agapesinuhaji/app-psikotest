<?php

namespace App\Filament\Psikolog\Resources\PsikologProfiles\Pages;

use App\Filament\Psikolog\Resources\PsikologProfiles\PsikologProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListPsikologProfiles extends ListRecords
{
    protected static string $resource = PsikologProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
