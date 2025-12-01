<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verification_at'),
                TextInput::make('username'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('place_of_birth'),
                DatePicker::make('date_of_birth'),
                TextInput::make('age')
                    ->numeric(),
                TextInput::make('gender'),
                TextInput::make('last_education'),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('photo'),
                Toggle::make('is_admin')
                    ->required(),
                TextInput::make('status'),
                TextInput::make('batch_id')
                    ->numeric(),
            ]);
    }
}
