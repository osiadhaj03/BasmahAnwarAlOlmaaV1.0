<?php

namespace App\Filament\Student\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;

class AttendanceHistoryWidget extends BaseWidget
{
    protected ?string $heading = 'سجل الحضور للطالب في جميع الدورات المسجل فيها ';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->where('student_id', auth()->id())
                    ->with(['lecture.lesson.teacher', 'lecture.lesson'])
            )
            ->columns([
                TextColumn::make('lecture.lesson.title')
                    ->label('اسم الدورة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),
                
                TextColumn::make('lecture.lesson.teacher.name')
                    ->label('المعلم')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),
                

                
                TextColumn::make('attendance_date')
                    ->label('وقت تسجيل الحضور')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                
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
                
                //TextColumn::make('attendance_method')
                //    ->label('طريقة التسجيل')
                //    ->formatStateUsing(fn (string $state): string => match ($state) {
                //        'code' => 'كود الحضور',
                //        'manual' => 'يدوي',
                //        'auto' => 'تلقائي',
                //        default => $state,
                //    })
                //    ->badge()
                //    ->color('gray'),
                
                TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->placeholder('لا توجد ملاحظات')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الحضور')
                    ->options([
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'معذور',
                    ]),
                
                Tables\Filters\SelectFilter::make('attendance_method')
                    ->label('طريقة التسجيل')
                    ->options([
                        'code' => 'كود الحضور',
                        'manual' => 'يدوي',
                        'auto' => 'تلقائي',
                    ]),
            ]);
    }
}