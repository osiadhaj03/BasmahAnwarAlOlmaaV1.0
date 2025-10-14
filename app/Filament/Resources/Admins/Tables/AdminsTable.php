<?php

namespace App\Filament\Resources\Admins\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('employee_id')
                    ->label('رقم الموظف')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-identification'),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('admin_level')
                    ->label('مستوى الإدارة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'moderator' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'مدير عام',
                        'admin' => 'إداري',
                        'moderator' => 'مشرف',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('department')
                    ->label('القسم')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'academic' => 'success',
                        'student_affairs' => 'info',
                        'hr' => 'warning',
                        'finance' => 'danger',
                        'it' => 'primary',
                        'general' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'academic' => 'الشؤون الأكاديمية',
                        'student_affairs' => 'شؤون الطلاب',
                        'hr' => 'الموارد البشرية',
                        'finance' => 'الشؤون المالية',
                        'it' => 'تقنية المعلومات',
                        'general' => 'الإدارة العامة',
                        default => 'غير محدد',
                    })
                    ->placeholder('غير محدد')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('hire_date')
                    ->label('تاريخ التوظيف')
                    ->date('Y-m-d')
                    ->placeholder('غير محدد')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('admin_level')
                    ->label('مستوى الإدارة')
                    ->options([
                        'super_admin' => 'مدير عام',
                        'admin' => 'إداري',
                        'moderator' => 'مشرف',
                    ])
                    ->placeholder('جميع المستويات'),

                SelectFilter::make('department')
                    ->label('القسم')
                    ->options([
                        'academic' => 'الشؤون الأكاديمية',
                        'student_affairs' => 'شؤون الطلاب',
                        'hr' => 'الموارد البشرية',
                        'finance' => 'الشؤون المالية',
                        'it' => 'تقنية المعلومات',
                        'general' => 'الإدارة العامة',
                    ])
                    ->placeholder('جميع الأقسام'),

                SelectFilter::make('is_active')
                    ->label('حالة الحساب')
                    ->options([
                        '1' => 'نشط',
                        '0' => 'غير نشط',
                    ])
                    ->placeholder('جميع الحالات'),
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
