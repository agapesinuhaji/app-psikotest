<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email')
                                    ->label('Email Address'),
                                TextEntry::make('phone'),
                                TextEntry::make('role'),
                                IconEntry::make('status')
                                    ->label('Status')
                                    ->icon(fn (string $state) => match ($state) {
                                        'active' => 'heroicon-o-check-circle',
                                        'inactive' => 'heroicon-o-x-circle',
                                    })
                                    ->color(fn (string $state) => match ($state) {
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                    }),
                            ]),
                    ])
                    ->columnSpan(2),
                Section::make('')
                    ->schema([
                        ImageEntry::make('photo')
                            ->square()
                            ->height(350)
                            ->label('Profile Photo'),
                    ])
                    ->aside() // membuat panel terpisah dari data utama
                    ->columnSpan(1),
                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')->dateTime(),
                                TextEntry::make('updated_at')->dateTime(),
                                TextEntry::make('deleted_at')->dateTime(),
                            ]),
                    ])
                    ->collapsed(), // collapsible section otomatis tertutup
            ]);

    }
}
