<?php

namespace App\Filament\Client\Resources\ClientBatches\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class ClientBatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // =========================
            // BATCH INFO
            // =========================
            Section::make('Batch Information')
                ->schema([
                    Grid::make(1)->schema([

                        TextInput::make('name')
                            ->label('Batch Name')
                            ->required(),

                        // 🔥 set owner batch = client login
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ]),
                ])
                ->columnSpanFull(),

            // =========================
            // IMPORT EXCEL
            // =========================
            Section::make('Import Participants via Excel')
                ->visible(fn (string $operation) => $operation !== 'create')
                ->description('Upload file Excel untuk menambahkan banyak participant sekaligus')
                ->schema([

                    Placeholder::make('download_template')
                        ->content(new HtmlString(
                            '<a href="/client/batches/download-template" target="_blank" style="color:#2563eb;font-weight:600">
                                ⬇ Download Template Excel
                            </a>'
                        )),

                    FileUpload::make('participants_file')
                        ->label('Upload Excel File')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->disk('public')
                        ->directory('imports')
                        ->preserveFilenames()
                        ->helperText('Gunakan template sebagai format yang benar!'),
                ])
                ->columnSpanFull(),

            // =========================
            // PARTICIPANTS MANUAL
            // =========================
            Section::make('Participants')
                ->visible(fn (string $operation) => $operation !== 'create')
                ->description('Add batch participants manually')
                ->schema([

                    Repeater::make('users')
                        ->relationship() // Batch::users()
                        ->defaultItems(0)
                        ->schema([

                            Grid::make(2)->schema([

                                TextInput::make('name')
                                    ->required(),

                                TextInput::make('email')
                                    ->email(),

                                TextInput::make('place_of_birth')
                                    ->required(),

                                DatePicker::make('date_of_birth')
                                    ->required(),

                                Select::make('gender')
                                    ->options([
                                        'L' => 'Male',
                                        'P' => 'Female',
                                    ])
                                    ->required(),

                                TextInput::make('last_education'),

                                TextInput::make('phone'),

                                Hidden::make('is_active')
                                    ->default(0),

                                // 🔥 set role otomatis sebagai client
                                Hidden::make('role')
                                    ->default('client'),
                            ]),
                        ])
                        ->addActionLabel('Tambah Peserta')
                        ->columns(1)
                        ->deletable(true)
                        ->reorderable(false),

                ])
                ->columnSpanFull(),

        ]);
    }
}