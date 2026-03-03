<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;

class BatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Batch Information (Readonly)
            |--------------------------------------------------------------------------
            */
            Section::make('Batch Information')
                ->schema([
                    Grid::make(2)->schema([

                        TextInput::make('name')
                            ->label('Batch Name')
                            ->disabled(),

                        TextInput::make('status')
                            ->label('Status')
                            ->disabled(),

                        DateTimePicker::make('date')
                            ->label('Jadwal Test')
                            ->seconds(false)
                            ->required()
                            ->columnSpanFull(),

                    ]),
                ])
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | Participants (Read Only)
            |--------------------------------------------------------------------------
            */
            Section::make('Participants')
                ->description('Daftar peserta batch ini')
                ->schema([

                    Repeater::make('users')
                        ->relationship()
                        ->schema([
                            Grid::make(4)->schema([

                                TextInput::make('name')
                                    ->label('Nama')
                                    ->disabled(),

                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->disabled(),

                                TextInput::make('place_of_birth')
                                    ->label('Tempat Lahir')
                                    ->disabled(),

                                TextInput::make('date_of_birth')
                                    ->label('Tanggal Lahir')
                                    ->formatStateUsing(fn ($state) =>
                                        \Carbon\Carbon::parse($state)->translatedFormat('d F Y')
                                    )
                                    ->disabled(),

                                TextInput::make('age')
                                    ->label('Umur')
                                    ->disabled(),

                                TextInput::make('last_education')
                                    ->label('Pendidikan')
                                    ->disabled(),

                                TextInput::make('nama_ayah')
                                    ->label('Nama Ayah')
                                    ->disabled(),
                            ])
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->disabled(),
                ])
                ->columnSpanFull(),
        ]);
    }
}