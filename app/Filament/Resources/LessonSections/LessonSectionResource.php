<?php

namespace App\Filament\Resources\LessonSections;

use App\Filament\Resources\LessonSections\Pages\CreateLessonSection;
use App\Filament\Resources\LessonSections\Pages\EditLessonSection;
use App\Filament\Resources\LessonSections\Pages\ListLessonSections;
use App\Filament\Resources\LessonSections\Pages\ViewLessonSection;
use App\Filament\Resources\LessonSections\Schemas\LessonSectionForm;
use App\Filament\Resources\LessonSections\Schemas\LessonSectionInfolist;
use App\Filament\Resources\LessonSections\Tables\LessonSectionsTable;
use App\Models\LessonSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LessonSectionResource extends Resource
{
    protected static ?string $model = LessonSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'LessonSection';

    public static function form(Schema $schema): Schema
    {
        return LessonSectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LessonSectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonSectionsTable::configure($table);
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
            'index' => ListLessonSections::route('/'),
            'create' => CreateLessonSection::route('/create'),
            'view' => ViewLessonSection::route('/{record}'),
            'edit' => EditLessonSection::route('/{record}/edit'),
        ];
    }
}
