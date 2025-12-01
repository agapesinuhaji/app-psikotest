<?php

namespace App\Filament\Resources\TypeQuestions;

use App\Filament\Resources\TypeQuestions\Pages\CreateTypeQuestion;
use App\Filament\Resources\TypeQuestions\Pages\EditTypeQuestion;
use App\Filament\Resources\TypeQuestions\Pages\ListTypeQuestions;
use App\Filament\Resources\TypeQuestions\Pages\ViewTypeQuestion;
use App\Filament\Resources\TypeQuestions\Schemas\TypeQuestionForm;
use App\Filament\Resources\TypeQuestions\Schemas\TypeQuestionInfolist;
use App\Filament\Resources\TypeQuestions\Tables\TypeQuestionsTable;
use App\Models\TypeQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TypeQuestionResource extends Resource
{
    protected static ?string $model = TypeQuestion::class;
    protected static ?string $navigationLabel = 'Question';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TypeQuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TypeQuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TypeQuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTypeQuestions::route('/'),
            'create' => CreateTypeQuestion::route('/create'),
            'view' => ViewTypeQuestion::route('/{record}'),
            'edit' => EditTypeQuestion::route('/{record}/edit'),
        ];
    }
}
