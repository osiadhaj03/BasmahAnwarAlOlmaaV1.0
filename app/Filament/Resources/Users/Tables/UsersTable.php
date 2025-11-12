<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'teacher' => 'warning',
                        'student' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'مدير',
                        'teacher' => 'معلم',
                        'student' => 'طالب',
                        default => $state,
                    })
                    ->sortable(),
                
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('student_id')
                    ->label('رقم الطالب')
                    ->searchable()
                    ->toggleable()
                    ->visible(fn ($record) => $record?->type === 'student'),
                
                TextColumn::make('employee_id')
                    ->label('رقم الموظف')
                    ->searchable()
                    ->toggleable()
                    ->visible(fn ($record) => in_array($record?->type, ['admin', 'teacher'])),
                
                TextColumn::make('department')
                    ->label('القسم')
                    ->searchable()
                    ->toggleable(),
                
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('last_login_at')
                    ->label('آخر دخول')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع المستخدم')
                    ->options([
                        'admin' => 'مدير',
                        'teacher' => 'معلم',
                        'student' => 'طالب',
                    ]),
                
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('جميع المستخدمين')
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط'),
                
                SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض'),
                EditAction::make()
                    ->label('تعديل'),
                Action::make('updatePassword')
                    ->label('تحديث كلمة السر')
                    ->icon('heroicon-m-lock-closed')
                    ->color('warning')
                    ->url(fn ($record) => route('filament.admin.resources.users.updatePassword', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
