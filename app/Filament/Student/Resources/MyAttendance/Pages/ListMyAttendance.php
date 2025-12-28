<?php

namespace App\Filament\Student\Resources\MyAttendance\Pages;

use App\Filament\Student\Resources\MyAttendance\MyAttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListMyAttendance extends ListRecords
{
    protected static string $resource = MyAttendanceResource::class;
}
