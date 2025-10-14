<?php

namespace App\Filament\Resources\AttendanceCodes\Pages;

use App\Filament\Resources\AttendanceCodes\AttendanceCodeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceCode extends EditRecord
{
    protected static string $resource = AttendanceCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
