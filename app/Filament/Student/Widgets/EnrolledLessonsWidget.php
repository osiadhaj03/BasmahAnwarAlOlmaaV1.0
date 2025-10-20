<?php

namespace App\Filament\Student\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Builder;

class EnrolledLessonsWidget extends BaseWidget
{
    protected static ?string $heading = 'الدورات المسجل فيها';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lesson::query()
                    ->whereHas('students', function (Builder $query) {
                        $query->where('users.id', auth()->id());
                    })
                    ->with(['teacher', 'students'])
            )
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان الدورة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),
                
                TextColumn::make('teacher.name')
                    ->label('المعلم')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('start_date')
                    ->label('تاريخ بداية الدرس')
                    ->date('Y-m-d')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                
                TextColumn::make('start_time')
                    ->label('وقت البداية')
                    ->time('H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                
                TextColumn::make('end_time')
                    ->label('وقت النهاية')
                    ->time('H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
                
                TextColumn::make('location')
                    ->label('المكان')
                    ->searchable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-map-pin'),
                
                BadgeColumn::make('is_active')
                    ->label('الحالة')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'نشط' : 'غير نشط')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),
                
                TextColumn::make('students_count')
                    ->label('عدد الطلاب')
                    ->counts('students')
                    ->sortable()
                    ->icon('heroicon-o-users'),
            ])
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}