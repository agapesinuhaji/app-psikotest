<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches;

use App\Filament\Psikolog\Resources\PsikologBatches\Pages\ListPsikologBatches;
use App\Filament\Psikolog\Resources\PsikologBatches\Pages\ViewPsikologBatch;
use App\Filament\Psikolog\Resources\PsikologBatches\Schemas\PsikologBatchForm;
use App\Filament\Psikolog\Resources\PsikologBatches\Schemas\PsikologBatchInfolist;
use App\Filament\Psikolog\Resources\PsikologBatches\Tables\PsikologBatchesTable;
use App\Models\Batch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PsikologBatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Batches';
    protected static ?string $modelLabel = 'Batches';
    protected static ?string $pluralModelLabel = 'Batches';

    protected static ?string $slug = 'batches';

    public static function form(Schema $schema): Schema
    {
        return PsikologBatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PsikologBatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PsikologBatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPsikologBatches::route('/'),
            'view' => ViewPsikologBatch::route('/{record}'),
        ];
    }
}
