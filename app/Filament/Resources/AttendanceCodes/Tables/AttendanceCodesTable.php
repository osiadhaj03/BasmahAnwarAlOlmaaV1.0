<?php

namespace App\Filament\Resources\AttendanceCodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AttendanceCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson.title')
                    ->label('الدرس')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                
                TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->color('success'),
                
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('expires_at')
                    ->label('تاريخ انتهاء الصلاحية')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at < now() ? 'danger' : 'success'),
                
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->getStateUsing(function ($record) {
                        if (!$record->is_active) return 'غير نشط';
                        if ($record->expires_at < now()) return 'منتهي الصلاحية';
                        if ($record->max_usage && $record->usage_count >= $record->max_usage) return 'مستنفد';
                        return 'نشط';
                    })
                    ->colors([
                        'success' => 'نشط',
                        'danger' => ['غير نشط', 'منتهي الصلاحية', 'مستنفد'],
                    ]),
                
                TextColumn::make('usage_count')
                    ->label('مرات الاستخدام')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->max_usage 
                        ? "{$record->usage_count} / {$record->max_usage}" 
                        : $record->usage_count),
                
                TextColumn::make('createdBy.name')
                    ->label('أنشأ بواسطة')
                    ->placeholder('النظام')
                    ->toggleable(),
                
                TextColumn::make('deactivated_at')
                    ->label('تاريخ إلغاء التفعيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('نشط')
                    ->toggleable(),
                
                TextColumn::make('deactivatedBy.name')
                    ->label('ألغى التفعيل')
                    ->placeholder('لم يتم إلغاء التفعيل')
                    ->toggleable(),
                
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
                TernaryFilter::make('is_active')
                    ->label('نشط')
                    ->trueLabel('نشط فقط')
                    ->falseLabel('غير نشط فقط')
                    ->placeholder('الكل'),
                
                SelectFilter::make('lesson_id')
                    ->label('الدرس')
                    ->relationship('lesson', 'title')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('created_by')
                    ->label('أنشأ بواسطة')
                    ->relationship('createdBy', 'name')
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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
