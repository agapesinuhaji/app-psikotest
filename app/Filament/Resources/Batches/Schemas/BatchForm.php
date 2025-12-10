<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\DateTimePicker;

class BatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Batch Information')
                ->schema([
                    Grid::make(2)->schema([

                        TextInput::make('name')
                            ->label('Batch Name')
                            ->required(),
                        
                        TextInput::make('status')
                            ->label('Status')
                            ->default('standby')
                            ->readOnly(),

                    ]),
                ])
                ->collapsible()
                ->collapsed(false)
                ->columnSpanFull(),

                

            Section::make('Participants')
                ->description('Add batch participants')
                ->disabled(function ($get, $livewire) {
                    // Cek apakah ini halaman Edit
                    $isEdit = $livewire instanceof \Filament\Resources\Pages\EditRecord;

                    // Hanya disable jika sedang edit DAN status bukan standby
                    return $isEdit && $get('status') !== 'standby';
                })

                ->schema([
                    Repeater::make('users')
                        ->relationship()   // relasi ke Batch::users()
                        
                        ->afterStateHydrated(function ($component, $state) {
                            $state = array_values($state);

                            foreach ($state as $index => &$item) {
                                $item['_number'] = $index + 1;
                            }

                            $component->state($state);
                        })

                        ->afterStateUpdated(function ($component, $state) {
                            $state = array_values($state);

                            foreach ($state as $index => &$item) {
                                $item['_number'] = $index + 1;
                            }

                            $component->state($state);
                        })

                        ->itemLabel(fn ($state) => 'No. ' . ($state['_number'] ?? '-'))

                        ->schema([
                            Grid::make(5)->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required(),

                                TextInput::make('place_of_birth')
                                    ->label('Place of Birth')
                                    ->required(),

                                DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->required(),

                                Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'L' => 'Male',
                                        'P' => 'Female',
                                    ])
                                    ->required(),

                                TextInput::make('last_education')
                                    ->label('Last Education'),
                                
                                Radio::make('is_active')
                                    ->label('Status')
                                    ->options([
                                        0 => 'Nonaktif',
                                    ])
                                    ->default(0)


                            ]),
                        ])

                        ->addActionLabel('Add Participant')
                        ->columns(1)
                        ->deletable(true)
                        ->reorderable(false),
                ])
                ->columnSpanFull(),


        ]);
    }
}
