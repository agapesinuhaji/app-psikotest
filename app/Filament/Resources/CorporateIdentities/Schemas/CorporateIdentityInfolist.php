<?php

namespace App\Filament\Resources\CorporateIdentities\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class CorporateIdentityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('psikolog'),
                TextEntry::make('strk_sik'),
                TextEntry::make('sipp_sippk'),
                TextEntry::make('address'),
                ImageEntry::make('logo')
                    ->label('Logo')
                    ->height(150)
                    ->circular(), // opsional
                
            ]);
    }
}
