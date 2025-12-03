<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),
                TextInput::make('password')
                    ->password()
                    ->confirmed()
                    ->required(fn ($context) => $context === 'create')
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),

                TextInput::make('password_confirmation')
                    ->label('Confirmation Password')
                    ->password()
                    ->required(fn ($context) => $context === 'create') // hanya required saat create
                    ->revealable()
                    ->dehydrated(false)
                    ->same('password')
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),
                TextInput::make('phone')
                    ->tel()
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),
                TextInput::make('address')
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),
                FileUpload::make('photo')
                    ->label('Photo')
                    ->image() // hanya izinkan file gambar
                    ->directory('user') // folder penyimpanan (opsional)
                    ->maxSize(2048) // batas ukuran 2MB (opsional)
                    ->imageEditor() // aktifkan editor crop/rotate (opsional)
                    ->disabled(fn ($context, $record) =>
                        $context === 'edit' && auth()->user()->role === 'admin'
                    ),
                Select::make('is_admin')
                    ->label('Role')
                    ->options([
                        '1' => 'Admin',
                    ])
                    ->required()
                    ->default('1'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
                    ->default('active')
                    ->native(false), // agar tampilannya lebih modern (optional)
            ]);
    }
}
