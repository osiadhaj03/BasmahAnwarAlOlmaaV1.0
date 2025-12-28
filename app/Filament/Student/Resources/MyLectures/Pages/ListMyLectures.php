<?php

namespace App\Filament\Student\Resources\MyLectures\Pages;

use App\Filament\Student\Resources\MyLectures\MyLecturesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyLectures extends ListRecords
{
    protected static string $resource = MyLecturesResource::class;
}
