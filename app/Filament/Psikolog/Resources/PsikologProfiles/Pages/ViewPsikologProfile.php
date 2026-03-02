<?php

namespace App\Filament\Psikolog\Resources\PsikologProfiles\Pages;

use App\Filament\Psikolog\Resources\PsikologProfiles\PsikologProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPsikologProfile extends ViewRecord
{
    protected static string $resource = PsikologProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
