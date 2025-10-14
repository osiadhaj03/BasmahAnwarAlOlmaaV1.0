<?php

namespace App\Filament\Resources\AttendanceCodes\Pages;

use App\Filament\Resources\AttendanceCodes\AttendanceCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceCode extends CreateRecord
{
    protected static string $resource = AttendanceCodeResource::class;
}
