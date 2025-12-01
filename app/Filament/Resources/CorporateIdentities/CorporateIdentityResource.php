<?php

namespace App\Filament\Resources\CorporateIdentities;

use App\Filament\Resources\CorporateIdentities\Pages\EditCorporateIdentity;
use App\Filament\Resources\CorporateIdentities\Pages\ListCorporateIdentities;
use App\Filament\Resources\CorporateIdentities\Pages\ViewCorporateIdentity;
use App\Filament\Resources\CorporateIdentities\Schemas\CorporateIdentityForm;
use App\Filament\Resources\CorporateIdentities\Schemas\CorporateIdentityInfolist;
use App\Filament\Resources\CorporateIdentities\Tables\CorporateIdentitiesTable;
use App\Models\CorporateIdentity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorporateIdentityResource extends Resource
{
    protected static ?string $model = CorporateIdentity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CorporateIdentityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CorporateIdentityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorporateIdentitiesTable::configure($table);
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
            'index' => ListCorporateIdentities::route('/'),
            'view' => ViewCorporateIdentity::route('/{record}'),
            'edit' => EditCorporateIdentity::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
