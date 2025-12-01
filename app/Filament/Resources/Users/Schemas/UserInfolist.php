<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verification_at')
                    ->dateTime(),
                TextEntry::make('username'),
                TextEntry::make('place_of_birth'),
                TextEntry::make('date_of_birth')
                    ->date(),
                TextEntry::make('age')
                    ->numeric(),
                TextEntry::make('gender'),
                TextEntry::make('last_education'),
                TextEntry::make('phone'),
                TextEntry::make('photo'),
                IconEntry::make('is_admin')
                    ->boolean(),
                TextEntry::make('status'),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('batch_id')
                    ->numeric(),
            ]);
    }
}
