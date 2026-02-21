<?php

namespace App\Filament\Client\Resources\ClientProfiles;

use App\Filament\Client\Resources\ClientProfiles\Pages\EditClientProfile;
use App\Filament\Client\Resources\ClientProfiles\Pages\ListClientProfiles;
use App\Filament\Client\Resources\ClientProfiles\Schemas\ClientProfileForm;
use App\Filament\Client\Resources\ClientProfiles\Tables\ClientProfilesTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Company Profile';
    protected static ?string $modelLabel = 'Company Profile';
    protected static ?string $pluralModelLabel = 'Company Profile';

    protected static ?string $slug = 'company-profile';

    /**
     * 🔐 Filter hanya data milik user login
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', auth()->id());
    }

    /**
     * 🔐 Proteksi akses record
     */
    public static function canView($record): bool
    {
        return $record->id === auth()->id();
    }

    public static function canEdit($record): bool
    {
        return $record->id === auth()->id();
    }

    /**
     * ❌ Client tidak boleh create / delete profile
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ClientProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientProfiles::route('/'),
            'edit' => EditClientProfile::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'client';
    }
}