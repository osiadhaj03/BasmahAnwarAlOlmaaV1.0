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
                    ->label('عنوان الدورة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('teacher.name')
                    ->label('المعلم')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('lessonSection.name')
                    ->label('القسم')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد')
                    ->badge()
                    ->color(fn ($record) => $record->lessonSection?->color ?? 'gray'),
                
                TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('Y-m-d')
                    ->sortable(),
                
                TextColumn::make('end_date')
                    ->label('تاريخ النهاية')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('lesson_days_arabic')
                    ->label('أيام الدورة')
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('start_time')
                    ->label('وقت البداية')
                    ->time('H:i')
                    ->sortable(),
                
                TextColumn::make('end_time')
                    ->label('وقت النهاية')
                    ->time('H:i')
                    ->sortable(),
                
                BadgeColumn::make('location_type')
                    ->label('نوع المكان')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'online' => 'أونلاين',
                        'offline' => 'حضوري',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'online',
                        'warning' => 'offline',
                    ]),
                
                TextColumn::make('location_details')
                    ->label('تفاصيل المكان')
                    ->searchable()
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                
                BadgeColumn::make('is_recurring')
                    ->label('متكرر')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'نعم' : 'لا')
                    ->colors([
                        'success' => true,
                        'secondary' => false,
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                    ->label('حالة الدورة')
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
                
                SelectFilter::make('lesson_section_id')
                    ->label('القسم')
                    ->relationship('lessonSection', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('location_type')
                    ->label('نوع المكان')
                    ->options([
                        'online' => 'أونلاين',
                        'offline' => 'حضوري',
                    ]),
                
                SelectFilter::make('is_recurring')
                    ->label('الدورات المتكررة')
                    ->options([
                        1 => 'متكرر',
                        0 => 'غير متكرر',
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
            ->defaultSort('start_date', 'desc')
            ->striped();
    }
}
