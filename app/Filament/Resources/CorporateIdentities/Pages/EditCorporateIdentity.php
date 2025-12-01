<?php

namespace App\Filament\Resources\CorporateIdentities\Pages;

use App\Filament\Resources\CorporateIdentities\CorporateIdentityResource;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCorporateIdentity extends EditRecord
{
    protected static string $resource = CorporateIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            RestoreAction::make(),
        ];
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

}
