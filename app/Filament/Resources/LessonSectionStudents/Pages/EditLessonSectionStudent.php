<?php

namespace App\Filament\Resources\LessonSectionStudents\Pages;

use App\Filament\Resources\LessonSectionStudents\LessonSectionStudentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLessonSectionStudent extends EditRecord
{
    protected static string $resource = LessonSectionStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
