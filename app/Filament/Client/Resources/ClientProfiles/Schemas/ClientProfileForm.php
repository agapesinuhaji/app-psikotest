<?php

namespace App\Filament\Client\Resources\ClientProfiles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Hash;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ClientProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Perusahaan')
                    ->schema([

                        Grid::make(2)->schema([

                            TextInput::make('name')
                                ->label('Nama Perusahaan')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('phone')
                                ->label('Phone')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('address')
                                ->label('Address')
                                ->columnSpanFull(),
                        ]),

                        FileUpload::make('photo')
                            ->label('Photo / Logo')
                            ->image()
                            ->directory('client-logo')
                            ->imagePreviewHeight('120'),
                    ]),

                Section::make('Ubah Password')
                    ->schema([

                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->minLength(6)
                            ->same('password_confirmation')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->placeholder('Kosongkan jika tidak ingin mengubah password'),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->dehydrated(false),
                    ])
                    ->collapsed(),

            ]);
    }
}