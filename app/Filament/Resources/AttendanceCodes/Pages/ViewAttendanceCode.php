<?php

namespace App\Filament\Resources\AttendanceCodes\Pages;

use App\Filament\Resources\AttendanceCodes\AttendanceCodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceCode extends ViewRecord
{
    protected static string $resource = AttendanceCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
