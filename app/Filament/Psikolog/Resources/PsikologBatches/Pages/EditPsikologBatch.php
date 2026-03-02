<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches\Pages;

use App\Filament\Psikolog\Resources\PsikologBatches\PsikologBatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPsikologBatch extends EditRecord
{
    protected static string $resource = PsikologBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
