<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches\Pages;

use App\Filament\Psikolog\Resources\PsikologBatches\PsikologBatchResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPsikologBatch extends ViewRecord
{
    protected static string $resource = PsikologBatchResource::class;

    public ?string $participantSearch = '';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
