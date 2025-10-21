<?php

namespace App\Filament\Resources\LessonSectionStudents\Pages;

use App\Filament\Resources\LessonSectionStudents\LessonSectionStudentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLessonSectionStudent extends ViewRecord
{
    protected static string $resource = LessonSectionStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
