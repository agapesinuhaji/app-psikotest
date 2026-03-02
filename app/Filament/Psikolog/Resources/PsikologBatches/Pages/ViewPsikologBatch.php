<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches\Pages;

use App\Filament\Psikolog\Resources\PsikologBatches\PsikologBatchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPsikologBatch extends ViewRecord
{
    protected static string $resource = PsikologBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
