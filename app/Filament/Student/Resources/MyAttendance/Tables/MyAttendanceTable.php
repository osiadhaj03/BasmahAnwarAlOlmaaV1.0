<?php

namespace App\Filament\Student\Resources\MyAttendance\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MyAttendanceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lecture.lesson.title')
                    ->label('الدورة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('lecture.title')
                    ->label('المحاضرة')
                    ->searchable(),

                TextColumn::make('attendance_date')
                    ->label('وقت الحضور')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'excused',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'معذور',
                        default => $state,
                    }),

                BadgeColumn::make('attendance_method')
                    ->label('طريقة التسجيل')
                    ->colors([
                        'primary' => 'code',
                        'gray' => 'manual',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'code' => 'كود',
                        'manual' => 'يدوي',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'معذور',
                    ]),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->paginated([10, 25]);
    }
}
