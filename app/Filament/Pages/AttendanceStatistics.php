<?php

namespace App\Filament\Pages;

use App\Models\Lesson;
use App\Models\LessonSection;
use App\Models\User;
use App\Models\Attendance;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class AttendanceStatistics extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.attendance-statistics';
    protected static ?string $navigationLabel = 'إحصائيات الحضور';
    protected static ?string $title = 'إحصائيات الحضور والغياب';
    protected static ?int $navigationSort = 4;

    public ?int $selectedSection = null;
    public ?int $selectedTeacher = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedSection')
                    ->label('الدورة')
                    ->options(LessonSection::pluck('name', 'id'))
                    ->placeholder('جميع الدورات')
                    ->live(),
                
                Select::make('selectedTeacher')
                    ->label('المعلم')
                    ->options(User::where('type', 'teacher')->pluck('name', 'id'))
                    ->placeholder('جميع المعلمين')
                    ->live(),
                
                DatePicker::make('startDate')
                    ->label('من تاريخ')
                    ->live(),
                
                DatePicker::make('endDate')
                    ->label('إلى تاريخ')
                    ->live(),
            ])
            ->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('student.name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('lesson.title')
                    ->label('الدرس')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('lesson.lessonSection.name')
                    ->label('الدورة')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('lesson.teacher.name')
                    ->label('المعلم')
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
                
                TextColumn::make('attendance_method')
                    ->label('طريقة التسجيل')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'code' => 'بالكود',
                        'manual' => 'يدوي',
                        'auto' => 'تلقائي',
                        default => $state,
                    }),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getTableQuery(): Builder
    {
        $query = Attendance::with(['student', 'lesson.teacher', 'lesson.lessonSection']);

        if ($this->selectedSection) {
            $query->whereHas('lesson', function (Builder $q) {
                $q->where('lesson_section_id', $this->selectedSection);
            });
        }

        if ($this->selectedTeacher) {
            $query->whereHas('lesson', function (Builder $q) {
                $q->where('teacher_id', $this->selectedTeacher);
            });
        }

        if ($this->startDate) {
            $query->where('attendance_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('attendance_date', '<=', $this->endDate . ' 23:59:59');
        }

        return $query;
    }

    public function getStats(): array
    {
        $query = $this->getTableQuery();
        
        $totalAttendances = $query->count();
        $presentCount = $query->where('status', 'present')->count();
        $absentCount = $query->where('status', 'absent')->count();
        $lateCount = $query->where('status', 'late')->count();
        
        $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

        return [
            Stat::make('إجمالي السجلات', $totalAttendances)
                ->description('إجمالي سجلات الحضور')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
            
            Stat::make('نسبة الحضور', $attendanceRate . '%')
                ->description($presentCount . ' من ' . $totalAttendances)
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('الغياب', $absentCount)
                ->description('عدد حالات الغياب')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
            
            Stat::make('التأخير', $lateCount)
                ->description('عدد حالات التأخير')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}