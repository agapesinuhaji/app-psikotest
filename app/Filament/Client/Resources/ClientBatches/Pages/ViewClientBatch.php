<?php

namespace App\Filament\Client\Resources\ClientBatches\Pages;

use App\Filament\Client\Resources\ClientBatches\ClientBatchResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class ViewClientBatch extends ViewRecord
{
    protected static string $resource = ClientBatchResource::class;

    // 🔥 STATE SEARCH
    public ?string $participantSearch = '';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Tambah Peserta'),

            // 🔍 SEARCH BUTTON
            // Action::make('search')
            //     ->label('Cari Peserta')
            //     ->icon('heroicon-m-magnifying-glass')
            //     ->form([
            //         TextInput::make('search')
            //             ->label('Nama / Username / NIK')
            //             ->placeholder('contoh: budi')
            //     ])
            //     ->action(function (array $data) {
            //         $this->participantSearch = $data['search'] ?? '';
            //     }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return \App\Filament\Client\Resources\ClientBatches\Schemas\ClientBatchInfolist::configure($schema);
    }
}