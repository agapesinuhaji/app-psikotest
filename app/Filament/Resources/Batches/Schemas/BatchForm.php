<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Schemas\Schema;
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

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'open'   => 'Open',
                                'closed' => 'Closed',
                            ])
                            ->required(),

                        DateTimePicker::make('start_time')
                            ->label('Start Time')
                            ->required(),

                        DateTimePicker::make('end_time')
                            ->label('End Time')
                            ->required(),
                    ]),
                ])
                ->collapsible()
                ->collapsed(false)
                ->columnSpanFull(),

                

            Section::make('Participants')
                ->description('Add batch participants')
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
