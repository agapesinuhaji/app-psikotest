<?php

namespace App\Filament\Resources\TypeQuestions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

class TypeQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Utama')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->hidden()
                                    ->dehydrated()
                                    ->required()
                                    ->default(fn ($get) => \Illuminate\Support\Str::slug($get('name')))
                                    ->afterStateHydrated(function ($state, $get, $set) {
                                        if (! $state) {
                                            $set('slug', \Illuminate\Support\Str::slug($get('name')));
                                        }
                                    }),

                                FileUpload::make('photo')
                                    ->image()
                                    ->directory('type-questions')
                                    ->imageEditor()
                                    ->disk('public')
                                    ->maxSize(2048),

                                // Tambahan baru dari migration
                                TextInput::make('duration')
                                    ->label('Durasi (menit)')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                                TextInput::make('description')
                                    ->label('Deskripsi')
                                    ->maxLength(500)
                                    ->nullable(),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(false),


                Repeater::make('questions')
                    ->label('List Of Questions')
                    ->relationship('questions')
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(true)
                    ->columnSpanFull()
                    ->deletable(true)
                    ->reorderable(false)

                    ->afterStateHydrated(function ($component, $state) {
                        // Paksa index jadi 0,1,2,3...
                        $state = array_values($state);

                        foreach ($state as $index => &$item) {
                            $item['_number'] = $index + 1;
                        }

                        $component->state($state);
                    })



                    // Saat terjadi penambahan / penghapusan item
                   ->afterStateUpdated(function ($component, $state) {

                        $state = array_values($state);

                        foreach ($state as $index => &$item) {
                            $item['_number'] = $index + 1;
                        }

                        $component->state($state);
                    })




                    // NOMOR URUT DI HEADER SEJAJAR DELETE BUTTON
                    ->itemLabel(fn ($state) => 'No. ' . ($state['_number'] ?? '-'))




                    ->schema([
                        RichEditor::make('question')
                            ->label('Question')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'bulletList', 'orderedList',
                                'link', 'attachFiles',   // <--- penting agar bisa upload gambar
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('questions/images')
                            ->columnSpanFull()
                            ->required(),

                        
                        Select::make('question_code')
                            ->label('Kode Question')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                                'E' => 'E',
                                '2 Option' => '2 Option',
                            ])
                            ->required()
                            ->columnSpanFull(),

                        Repeater::make('options')
                            ->relationship('options')
                            ->label('Options')
                            ->schema([
                                Grid::make(5) // total 5 kolom, bisa dibagi 4:1 (80% : 20%)
                                    ->schema([
                                        RichEditor::make('option')
                                            ->label('Option')
                                            ->toolbarButtons([
                                                'bold', 'italic', 'underline', 'strike',
                                                'bulletList', 'orderedList',
                                                'link', 'attachFiles',  // Penting agar bisa upload gambar
                                            ])
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('questions/options/images')
                                            ->columnSpan(4) // 4 dari 5 kolom = 80%
                                            ->required(),

                                        Select::make('value')
                                            ->label('Value')
                                            ->options([
                                                1 => 'True',
                                                0 => 'False',
                                            ])
                                            ->required()
                                            ->default(false)
                                            ->columnSpan(1),

                                    ]),
                            ])
                            ->columns(1) // repeater tetap satu baris per item
                            ->addActionLabel('Tambah Option')
                            ->columnSpanFull()
                            ->required(),                            
                  
                            
                        ]),
            ]);
    }
}
