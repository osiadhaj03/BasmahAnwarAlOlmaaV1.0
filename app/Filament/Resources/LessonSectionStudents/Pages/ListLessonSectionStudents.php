<?php

namespace App\Filament\Resources\LessonSectionStudents\Pages;

use App\Filament\Resources\LessonSectionStudents\LessonSectionStudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLessonSectionStudents extends ListRecords
{
    protected static string $resource = LessonSectionStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
