<?php

namespace App\Filament\Resources\Lectures\Tables;

use App\Models\Lesson;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LecturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان المحاضرة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lesson.title')
                    ->label('الدورة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lecture_number')
                    ->label('رقم المحاضرة')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('lecture_date')
                    ->label('تاريخ المحاضرة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('المدة')
                    ->formatStateUsing(fn ($state) => $state . ' دقيقة')
                    ->alignCenter(),

                TextColumn::make('location')
                    ->label('المكان')
                    ->searchable()
                    ->limit(30),

                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('lesson_id')
                    ->label('الدورة')
                    ->options(Lesson::all()->pluck('title', 'id'))
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('حالة المحاضرة')
                    ->options([
                        'scheduled' => 'مجدولة',
                        'ongoing' => 'جارية',
                        'completed' => 'مكتملة',
                        'cancelled' => 'ملغية',
                    ]),

                SelectFilter::make('is_mandatory')
                    ->label('المحاضرات الإجبارية')
                    ->options([
                        1 => 'إجبارية',
                        0 => 'اختيارية',
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
            ->defaultSort('lecture_date', 'desc');
    }
}
