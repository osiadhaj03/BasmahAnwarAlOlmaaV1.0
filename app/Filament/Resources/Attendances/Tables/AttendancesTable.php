<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson.title')
                    ->label('الدرس')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('student.name')
                    ->label('الطالب')
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('حالة الحضور')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'معذور',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'excused',
                    ]),
                
                TextColumn::make('attendance_date')
                    ->label('تاريخ الحضور')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                
                TextColumn::make('used_code')
                    ->label('الكود المستخدم')
                    ->searchable()
                    ->placeholder('لا يوجد'),
                
                BadgeColumn::make('attendance_method')
                    ->label('طريقة التسجيل')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'code' => 'بالكود',
                        'manual' => 'يدوي',
                        'auto' => 'تلقائي',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'code',
                        'secondary' => 'manual',
                        'success' => 'auto',
                    ]),
                
                TextColumn::make('marked_at')
                    ->label('وقت التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('markedBy.name')
                    ->label('سجل بواسطة')
                    ->placeholder('النظام')
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('حالة الحضور')
                    ->options([
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'معذور',
                    ]),
                
                SelectFilter::make('attendance_method')
                    ->label('طريقة التسجيل')
                    ->options([
                        'code' => 'بالكود',
                        'manual' => 'يدوي',
                        'auto' => 'تلقائي',
                    ]),
                
                SelectFilter::make('lesson_id')
                    ->label('الدرس')
                    ->relationship('lesson', 'title')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('student_id')
                    ->label('الطالب')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض'),
                EditAction::make()
                    ->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->striped();
    }
}
