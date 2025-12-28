<?php

namespace App\Filament\Student\Resources\MyCourses\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class MyCoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('اسم الدورة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),

                TextColumn::make('lessonSection.name')
                    ->label('القسم')
                    ->sortable()
                    ->badge(),

                TextColumn::make('teacher.name')
                    ->label('المعلم')
                    ->sortable()
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('lesson_days_arabic')
                    ->label('أيام الدرس'),

                TextColumn::make('start_time')
                    ->label('الوقت')
                    ->time('H:i'),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'cancelled',
                        'primary' => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'cancelled' => 'ملغي',
                        'completed' => 'مكتمل',
                        default => $state,
                    }),
            ])
            ->defaultSort('start_date', 'desc')
            ->paginated([10, 25]);
    }
}
