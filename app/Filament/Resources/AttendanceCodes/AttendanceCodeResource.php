<?php

namespace App\Filament\Resources\AttendanceCodes;

use App\Filament\Resources\AttendanceCodes\Pages\CreateAttendanceCode;
use App\Filament\Resources\AttendanceCodes\Pages\EditAttendanceCode;
use App\Filament\Resources\AttendanceCodes\Pages\ListAttendanceCodes;
use App\Filament\Resources\AttendanceCodes\Pages\ViewAttendanceCode;
use App\Filament\Resources\AttendanceCodes\Schemas\AttendanceCodeForm;
use App\Filament\Resources\AttendanceCodes\Schemas\AttendanceCodeInfolist;
use App\Filament\Resources\AttendanceCodes\Tables\AttendanceCodesTable;
use App\Models\AttendanceCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttendanceCodeResource extends Resource
{
    protected static ?string $model = AttendanceCode::class;

    protected static ?string $navigationLabel = 'أكواد الحضور';
    
    protected static ?string $modelLabel = 'كود حضور';
    
    protected static ?string $pluralModelLabel = 'أكواد الحضور';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return AttendanceCodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceCodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceCodes::route('/'),
            'create' => CreateAttendanceCode::route('/create'),
            'view' => ViewAttendanceCode::route('/{record}'),
            'edit' => EditAttendanceCode::route('/{record}/edit'),
        ];
    }
}
