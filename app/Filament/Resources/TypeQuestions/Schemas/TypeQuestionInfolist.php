<?php

namespace App\Filament\Resources\TypeQuestions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;



class TypeQuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // GRID 1
                Section::make('Informasi Utama')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('name')
                                    ->size('xl')         // lebih besar
                                    ->weight('bold')     // tebal
                                    ->alignStart(),

                                ImageEntry::make('photo')
                                    ->disk('public')
                                    ->width(120)
                                    ->alignStart(),


                                // Tambahan baru
                                TextEntry::make('duration')
                                    ->label('Durasi (menit)')
                                    ->numeric()
                                    ->default(60),

                                TextEntry::make('description')
                                    ->label('Deskripsi'),

                                TextEntry::make('status')
                                    ->label('status'),
                            ]),
                    ]),


                // GRID 2
                Section::make('Informasi Waktu')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Diubah')
                                    ->dateTime(),
                            ]),
                    ]),

                // GRID 3 - List Soal & Option
                Section::make('Daftar Soal')
                    ->columnSpanFull()
                    ->schema(function ($record) {
                        $components = [];

                        foreach ($record->questions as $questionIndex => $question) {
                            $components[] = Section::make('Question ' . ($questionIndex + 1))
                                ->collapsible()
                                ->collapsed(true)
                                ->schema([
                                    TextEntry::make('question_'.$question->id)
                                        ->label('Pertanyaan')
                                        ->getStateUsing(fn () => $question->question)
                                        ->html()
                                        ->columnSpanFull(),

                                    // Grid untuk option + value
                                    ...collect($question->options)->map(function($option, $optionIndex) {
                                        return [
                                            Grid::make(3) // total 3 kolom
                                                ->schema([
                                                    TextEntry::make('option_'.$option->id)
                                                        ->label('Option ' . ($optionIndex + 1))
                                                        ->getStateUsing(fn () => $option->option)
                                                        ->html()
                                                        ->columnSpan(2), // 2/3 untuk option

                                                    TextEntry::make('value_'.$option->id)
                                                        ->label('Value')
                                                        ->getStateUsing(fn () => (int) $option->value)
                                                        ->formatStateUsing(fn ($state) =>
                                                            $state == 1
                                                                ? '<span style="color: #16a34a;">✓ True</span>'   // green-600
                                                                : '<span style="color: #dc2626;">✕ False</span>'  // red-600
                                                        )
                                                        ->html()
                                                        ->columnSpan(1),

                                                ]),
                                        ];
                                    })->flatten(1)->toArray(),
                                ]);
                        }

                        return $components;
                    }),






            ]);
    }
}
