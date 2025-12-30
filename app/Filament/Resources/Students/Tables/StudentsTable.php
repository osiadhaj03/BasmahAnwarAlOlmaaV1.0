<?php

namespace App\Filament\Resources\Students\Tables;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
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
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Lecture;
use App\Models\LessonSection;

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
                
                TextColumn::make('enrolled_sections_count')
                    ->label('عدد الدبلومات المسجل فيها')
                    ->getStateUsing(function ($record) {
                        return $record->enrolledSections()->count();
                    })
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-bookmark'),
                
                
                
                TextColumn::make('monthly_lectures_count')
                    ->label('عدد المحاضرات هذا الشهر')
                    ->getStateUsing(function ($record) {
                        // عدد المحاضرات المفتوحة هذا الشهر في الدورات الإجبارية المسجل فيها
                        $sectionIds = $record->enrolledSections()->pluck('lessons_sections.id')->toArray();
                        if (empty($sectionIds)) {
                            return 0;
                        }
                        // الحصول على الدورات الإجبارية فقط
                        $lessonIds = Lesson::whereIn('lesson_section_id', $sectionIds)
                            ->where('is_mandatory', true)
                            ->pluck('id')
                            ->toArray();
                        if (empty($lessonIds)) {
                            return 0;
                        }
                        return Lecture::whereIn('lesson_id', $lessonIds)
                            ->whereMonth('lecture_date', Carbon::now()->month)
                            ->whereYear('lecture_date', Carbon::now()->year)
                            ->count();
                    })
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-calendar-days')
                    ->hidden(fn ($livewire) => !empty($livewire->tableFilters['date_range']['from']) || !empty($livewire->tableFilters['date_range']['to'])),
                
                                
                TextColumn::make('monthly_attendances_count')
                    ->label('الحضور هذا الشهر')
                    ->getStateUsing(function ($record) {
                        // حساب الحضور فقط للدورات الإجبارية
                        return $record->attendances()
                            ->where('status', 'present')
                            ->whereMonth('attendance_date', Carbon::now()->month)
                            ->whereYear('attendance_date', Carbon::now()->year)
                            ->whereHas('lecture.lesson', function ($query) {
                                $query->where('is_mandatory', true);
                            })
                            ->count();
                    })
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->sortable()
                    ->hidden(fn ($livewire) => !empty($livewire->tableFilters['date_range']['from']) || !empty($livewire->tableFilters['date_range']['to'])),
                
                TextColumn::make('absence_price')
                    ->label('سعر الغياب')
                    ->getStateUsing(function ($record) {
                        $data = $record->calculateAbsencePenalty();
                        return $data['absence_price'];
                    })
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->hidden(fn ($livewire) => !empty($livewire->tableFilters['date_range']['from']) || !empty($livewire->tableFilters['date_range']['to'])),
                
                TextColumn::make('monthly_absence_count')
                    ->label('عدد الغيابات')
                    ->getStateUsing(function ($record) {
                        $data = $record->calculateAbsencePenalty();
                        return $data['absence_count'];
                    })
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->hidden(fn ($livewire) => !empty($livewire->tableFilters['date_range']['from']) || !empty($livewire->tableFilters['date_range']['to'])),
                
                TextColumn::make('absence_penalty')
                    ->label('غرامة الغياب')
                    ->getStateUsing(function ($record) {
                        $data = $record->calculateAbsencePenalty();
                        return $data['penalty_amount'];
                    })
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state . ' ₪')
                    ->hidden(fn ($livewire) => !empty($livewire->tableFilters['date_range']['from']) || !empty($livewire->tableFilters['date_range']['to'])),
                
                
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
                    ->label('عدد المحاضرات (الفترة)')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        // حساب المحاضرات فقط للدورات الإجبارية
                        $query = Lecture::query()
                            ->whereHas('lesson', function ($q) {
                                $q->where('is_mandatory', true);
                            });
                        
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
                    ->hidden(fn ($livewire) => empty($livewire->tableFilters['date_range']['from']) && empty($livewire->tableFilters['date_range']['to'])),

                TextColumn::make('attendance_count')
                    ->label('عدد الحضور (الفترة)')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        // حساب الحضور فقط للدورات الإجبارية
                        $query = Attendance::where('student_id', $record->id)
                            ->whereIn('status', ['present', 'late'])
                            ->whereHas('lecture.lesson', function ($q) {
                                $q->where('is_mandatory', true);
                            });
                        
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
                    ->hidden(fn ($livewire) => empty($livewire->tableFilters['date_range']['from']) && empty($livewire->tableFilters['date_range']['to'])),

                TextColumn::make('absence_count')
                    ->label('عدد الغياب (الفترة)')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        // حساب الغياب فقط للدورات الإجبارية
                        $query = Attendance::where('student_id', $record->id)
                            ->where('status', 'absent')
                            ->whereHas('lecture.lesson', function ($q) {
                                $q->where('is_mandatory', true);
                            });
                        
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
                    ->hidden(fn ($livewire) => empty($livewire->tableFilters['date_range']['from']) && empty($livewire->tableFilters['date_range']['to'])),

                TextColumn::make('attendance_percentage')
                    ->label('نسبة الحضور (الفترة)')
                    ->getStateUsing(function ($record, $livewire) {
                        $filters = $livewire->tableFilters;
                        $from = $filters['date_range']['from'] ?? null;
                        $to = $filters['date_range']['to'] ?? null;
                        
                        if (!$from && !$to) {
                            return '-';
                        }
                        
                        // حساب المحاضرات فقط للدورات الإجبارية
                        $lectureQuery = Lecture::query()
                            ->whereHas('lesson', function ($q) {
                                $q->where('is_mandatory', true);
                            });
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
                            ->whereIn('status', ['present', 'late'])
                            ->whereHas('lecture.lesson', function ($q) {
                                $q->where('is_mandatory', true);
                            });
                        
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
                    ->hidden(fn ($livewire) => empty($livewire->tableFilters['date_range']['from']) && empty($livewire->tableFilters['date_range']['to'])),
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
                        
                        $from = $data['from'] ? Carbon::parse($data['from'])->format('Y-m-d') : 'البداية';
                        $to = $data['to'] ? Carbon::parse($data['to'])->format('Y-m-d') : 'الآن';
                        
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
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير')
                    ->fileName('Students')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                    FilamentExportBulkAction::make('export')
                        ->label('تصدير المحدد')
                        ->fileName('Selected_Students')
                        ->defaultFormat('xlsx')
                        ->defaultPageOrientation('landscape'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
