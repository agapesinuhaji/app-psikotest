<?php

namespace App\Filament\Client\Resources\ClientBatches;

use App\Filament\Client\Resources\ClientBatches\Pages\CreateClientBatch;
use App\Filament\Client\Resources\ClientBatches\Pages\EditClientBatch;
use App\Filament\Client\Resources\ClientBatches\Pages\ListClientBatches;
use App\Filament\Client\Resources\ClientBatches\Schemas\ClientBatchForm;
use App\Filament\Client\Resources\ClientBatches\Tables\ClientBatchesTable;
use App\Models\Batch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;



class ClientBatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Batch';

    protected static ?string $navigationLabel = 'Batch';
    protected static ?string $modelLabel = 'Batch';
    protected static ?string $pluralModelLabel = 'Batch';

    protected static ?string $slug = 'batches';

    public static function form(Schema $schema): Schema
    {
        return ClientBatchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientBatchesTable::configure($table);
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
            'index' => ListClientBatches::route('/'),
            'create' => CreateClientBatch::route('/create'),
            'edit' => EditClientBatch::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
