<?php

namespace App\Filament\Psikolog\Resources\PsikologProfiles;

use App\Filament\Psikolog\Resources\PsikologProfiles\Pages\EditPsikologProfile;
use App\Filament\Psikolog\Resources\PsikologProfiles\Pages\ListPsikologProfiles;
use App\Filament\Psikolog\Resources\PsikologProfiles\Schemas\PsikologProfileForm;
use App\Filament\Psikolog\Resources\PsikologProfiles\Tables\PsikologProfilesTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PsikologProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser; // Ikon user lebih cocok

    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $modelLabel = 'My Profile';
    protected static ?string $pluralModelLabel = 'My Profile';

    protected static ?string $slug = 'profile';

    /**
     * 🔥 FILTER: Hanya tampilkan data diri sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id', auth()->id());
    }

    /**
     * 🛡️ PERMISSION: Matikan Create & Delete
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return PsikologProfileForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return PsikologProfilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPsikologProfiles::route('/'),
            'edit' => EditPsikologProfile::route('/{record}/edit'),
        ];
    }
}