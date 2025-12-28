<?php

namespace App\Filament\Student\Resources\MyDiplomas;

use App\Filament\Student\Resources\MyDiplomas\Pages\ListMyDiplomas;
use App\Filament\Student\Resources\MyDiplomas\Tables\MyDiplomasTable;
use App\Models\LessonSection;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyDiplomasResource extends Resource
{
    protected static ?string $model = LessonSection::class;

    protected static ?string $slug = 'my-diplomas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'دبلوماتي';

    protected static ?string $modelLabel = 'دبلوم';

    protected static ?string $pluralModelLabel = 'دبلوماتي';
    
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return MyDiplomasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyDiplomas::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // عرض الأقسام التي الطالب مسجل فيها فقط
        return parent::getEloquentQuery()
            ->whereHas('enrolledStudents', function ($query) {
                $query->where('users.id', Auth::id())
                      ->where('lesson_section_student.enrollment_status', 'active');
            });
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
}
