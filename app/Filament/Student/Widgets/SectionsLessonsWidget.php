<?php

namespace App\Filament\Student\Widgets;

use App\Models\Lesson;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SectionsLessonsWidget extends BaseWidget
{
    protected static ?string $heading = 'دورات الأقسام المسجل بها';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $sectionIds = Auth::user()?->enrolledSections()
            ->wherePivot('enrollment_status', 'active')
            ->pluck('lessons_sections.id') ?? collect();

        $query = Lesson::query()
            ->with(['teacher', 'lessonSection'])
            ->when($sectionIds->isNotEmpty(), function (Builder $q) use ($sectionIds) {
                $q->whereIn('lesson_section_id', $sectionIds);
            }, function (Builder $q) {
                // في حالة عدم وجود أقسام مسجل بها، اجعل الاستعلام يعيد صفراً
                $q->whereRaw('1 = 0');
            });

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('title')
                    ->label('اسم الدورة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),

                TextColumn::make('lessonSection.name')
                    ->label('اسم القسم')
                    ->sortable()
                    ->icon('heroicon-o-rectangle-stack'),

                TextColumn::make('teacher.name')
                    ->label('المعلم')
                    ->sortable()
                    ->icon('heroicon-o-user'),

                BadgeColumn::make('status')
                    ->label('حالة الدورة')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشطة',
                        'inactive' => 'متوقفة',
                        'completed' => 'مكتملة',
                        default => 'غير محدد',
                    })
                    ->colors([
                        'success' => 'active',
                        'gray' => 'inactive',
                        'warning' => 'completed',
                    ]),

                TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('Y-m-d')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('end_date')
                    ->label('تاريخ النهاية')
                    ->date('Y-m-d')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('lesson_days_arabic')
                    ->label('أيام الدرس')
                    ->toggleable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }
}