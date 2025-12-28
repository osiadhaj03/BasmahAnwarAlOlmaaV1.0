<?php

namespace App\Filament\Student\Resources\MyLectures\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class MyLecturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان المحاضرة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('lesson.title')
                    ->label('الدورة')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('lecture_date')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('المدة')
                    ->suffix(' دقيقة'),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'secondary' => 'scheduled',
                        'success' => 'ongoing',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                    ])
                     ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'مجدولة',
                        'ongoing' => 'جارية',
                        'completed' => 'مكتملة',
                        'cancelled' => 'ملغية',
                        default => $state,
                    }),
            ])
            ->actions([
                Action::make('recording')
                    ->label('رابط التسجيل')
                    ->icon('heroicon-o-play-circle')
                    ->url(fn ($record) => $record->recording_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->recording_url)),
            ])
            ->defaultSort('lecture_date', 'desc')
            ->paginated([10, 25]);
    }
}
