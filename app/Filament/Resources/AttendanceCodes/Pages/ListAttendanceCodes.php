<?php

namespace App\Filament\Resources\AttendanceCodes\Pages;

use App\Filament\Resources\AttendanceCodes\AttendanceCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceCodes extends ListRecords
{
    protected static string $resource = AttendanceCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
