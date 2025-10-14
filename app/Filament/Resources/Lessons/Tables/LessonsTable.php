<?php

namespace App\Filament\Resources\Lessons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان الدرس')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('teacher.name')
                    ->label('المعلم')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('lesson_date')
                    ->label('تاريخ الدرس')
                    ->date('Y-m-d')
                    ->sortable(),
                
                TextColumn::make('start_time')
                    ->label('وقت البداية')
                    ->time('H:i')
                    ->sortable(),
                
                TextColumn::make('end_time')
                    ->label('وقت النهاية')
                    ->time('H:i')
                    ->sortable(),
                
                TextColumn::make('location')
                    ->label('المكان')
                    ->searchable()
                    ->placeholder('غير محدد'),
                
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'cancelled' => 'ملغي',
                        'completed' => 'مكتمل',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'active',
                        'danger' => 'cancelled',
                        'primary' => 'completed',
                    ]),
                
                TextColumn::make('max_students')
                    ->label('الحد الأقصى')
                    ->numeric()
                    ->sortable()
                    ->placeholder('غير محدود'),
                
                TextColumn::make('attendances_count')
                    ->label('عدد الحضور')
                    ->counts('attendances')
                    ->sortable(),
                
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
                    ->label('حالة الدرس')
                    ->options([
                        'active' => 'نشط',
                        'cancelled' => 'ملغي',
                        'completed' => 'مكتمل',
                    ]),
                
                SelectFilter::make('teacher_id')
                    ->label('المعلم')
                    ->relationship('teacher', 'name')
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
            ->defaultSort('lesson_date', 'desc')
            ->striped();
    }
}
