<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Attendance;
use App\Models\Lecture;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('student_id')
                    ->label('الرقم الجامعي')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('غير محدد'),
                
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-envelope'),
                
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-phone'),
                
                TextColumn::make('level')
                    ->label('المستوى الدراسي')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'freshman' => 'السنة الأولى',
                        'sophomore' => 'السنة الثانية',
                        'junior' => 'السنة الثالثة',
                        'senior' => 'السنة الرابعة',
                        'graduate' => 'دراسات عليا',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'freshman',
                        'success' => 'sophomore',
                        'warning' => 'junior',
                        'danger' => 'senior',
                        'info' => 'graduate',
                    ])
                    ->placeholder('غير محدد'),
                
                TextColumn::make('specialization')
                    ->label('التخصص')
                    ->searchable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-academic-cap'),
                
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'graduated' => 'متخرج',
                        'suspended' => 'موقوف',
                        'transferred' => 'محول',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'active',
                        'gray' => 'inactive',
                        'info' => 'graduated',
                        'danger' => 'suspended',
                        'warning' => 'transferred',
                    ]),
                
                TextColumn::make('date_of_birth')
                    ->label('تاريخ الميلاد')
                    ->date('Y-m-d')
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('emergency_contact_name')
                    ->label('جهة الاتصال في الطوارئ')
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // أعمدة إحصائيات الحضور (تظهر عند تفعيل فلتر التاريخ)
                TextColumn::make('total_lectures')
                    ->label('عدد المحاضرات')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        $query = Lecture::query();
                        
                        if ($from) {
                            $query->whereDate('lecture_date', '>=', $from);
                        }
                        if ($to) {
                            $query->whereDate('lecture_date', '<=', $to);
                        }
                        
                        return $query->count();
                    })
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('attendance_count')
                    ->label('عدد الحضور')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        $query = Attendance::where('student_id', $record->id)
                            ->whereIn('status', ['present', 'late']);
                        
                        if ($from || $to) {
                            $query->whereHas('lecture', function ($q) use ($from, $to) {
                                if ($from) {
                                    $q->whereDate('lecture_date', '>=', $from);
                                }
                                if ($to) {
                                    $q->whereDate('lecture_date', '<=', $to);
                                }
                            });
                        }
                        
                        return $query->count();
                    })
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('absence_count')
                    ->label('عدد الغياب')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        $query = Attendance::where('student_id', $record->id)
                            ->where('status', 'absent');
                        
                        if ($from || $to) {
                            $query->whereHas('lecture', function ($q) use ($from, $to) {
                                if ($from) {
                                    $q->whereDate('lecture_date', '>=', $from);
                                }
                                if ($to) {
                                    $q->whereDate('lecture_date', '<=', $to);
                                }
                            });
                        }
                        
                        return $query->count();
                    })
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('attendance_percentage')
                    ->label('نسبة الحضور')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        $lectureQuery = Lecture::query();
                        if ($from) {
                            $lectureQuery->whereDate('lecture_date', '>=', $from);
                        }
                        if ($to) {
                            $lectureQuery->whereDate('lecture_date', '<=', $to);
                        }
                        $totalLectures = $lectureQuery->count();
                        
                        if ($totalLectures === 0) {
                            return '0%';
                        }
                        
                        $attendanceQuery = Attendance::where('student_id', $record->id)
                            ->whereIn('status', ['present', 'late']);
                        
                        if ($from || $to) {
                            $attendanceQuery->whereHas('lecture', function ($q) use ($from, $to) {
                                if ($from) {
                                    $q->whereDate('lecture_date', '>=', $from);
                                }
                                if ($to) {
                                    $q->whereDate('lecture_date', '<=', $to);
                                }
                            });
                        }
                        $attendedCount = $attendanceQuery->count();
                        
                        $percentage = round(($attendedCount / $totalLectures) * 100, 1);
                        return $percentage . '%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === '-' => 'gray',
                        (float) rtrim($state, '%') >= 75 => 'success',
                        (float) rtrim($state, '%') >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('من تاريخ')
                            ->placeholder('اختر تاريخ البداية')
                            ->native(false)
                            ->displayFormat('Y-m-d'),
                        DatePicker::make('to')
                            ->label('إلى تاريخ')
                            ->placeholder('اختر تاريخ النهاية')
                            ->native(false)
                            ->displayFormat('Y-m-d'),
                    ])
                    ->columns(2)
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['from'] && !$data['to']) {
                            return null;
                        }
                        
                        $from = $data['from'] ? \Carbon\Carbon::parse($data['from'])->format('Y-m-d') : 'البداية';
                        $to = $data['to'] ? \Carbon\Carbon::parse($data['to'])->format('Y-m-d') : 'الآن';
                        
                        return "الفترة: {$from} - {$to}";
                    }),

                SelectFilter::make('status')
                    ->label('حالة الطالب')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'graduated' => 'متخرج',
                        'suspended' => 'موقوف',
                        'transferred' => 'محول',
                    ]),
                
                SelectFilter::make('level')
                    ->label('المستوى الدراسي')
                    ->options([
                        'freshman' => 'السنة الأولى',
                        'sophomore' => 'السنة الثانية',
                        'junior' => 'السنة الثالثة',
                        'senior' => 'السنة الرابعة',
                        'graduate' => 'دراسات عليا',
                    ]),
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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
