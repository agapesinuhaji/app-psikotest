<?php

namespace App\Filament\Resources\CorporateIdentities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

class CorporateIdentityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('psikolog'),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->image()                // hanya perbolehkan file gambar
                    ->directory('corporate-identity')     // folder penyimpanan
                    ->imageEditor()          // aktifkan editor gambar bawaan (crop, resize, dll)
                    ->maxSize(2048),         // maksimum 2MB
                TextInput::make('strk_sik')
                    ->label('STRIK / SIK'),
                TextInput::make('sipp_sippk')
                    ->label('SIPP / SIPPK'),
                TextInput::make('address'),
            ]);
    }
}
