<?php

namespace App\Filament\Client\Resources\ClientBatches\Pages;

use App\Filament\Client\Resources\ClientBatches\ClientBatchResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewClientBatch extends ViewRecord
{
    protected static string $resource = ClientBatchResource::class;

    // 🔍 STATE SEARCH PESERTA
    public ?string $participantSearch = '';

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Tambah Peserta
            |--------------------------------------------------------------------------
            */
            EditAction::make()
                ->label('Tambah Peserta')
                ->visible(fn () => $this->record->status === 'standby'),

            /*
            |--------------------------------------------------------------------------
            | Export PDF Peserta
            |--------------------------------------------------------------------------
            */
            Action::make('exportParticipants')
                ->label('Export PDF Peserta')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn () => in_array($this->record->status, ['paid', 'time set']))
                ->action(function () {

                    $users = $this->record->users;
                    $batch = $this->record;

                    $pdf = Pdf::loadView('pdf.user_list', [
                        'users' => $users,
                        'batch' => $batch,
                    ]);

                    $filename = 'batch-' . $batch->name . '.pdf';

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $filename
                    );
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return \App\Filament\Client\Resources\ClientBatches\Schemas\ClientBatchInfolist::configure($schema);
    }
}