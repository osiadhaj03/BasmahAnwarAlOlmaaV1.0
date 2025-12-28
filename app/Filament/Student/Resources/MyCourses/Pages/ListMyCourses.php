<?php

namespace App\Filament\Student\Resources\MyCourses\Pages;

use App\Filament\Student\Resources\MyCourses\MyCoursesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyCourses extends ListRecords
{
    protected static string $resource = MyCoursesResource::class;
}
