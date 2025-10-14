<?php

namespace App\Filament\Resources\Teachers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachersTable
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
                    ->icon('heroicon-o-identification'),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),

                BadgeColumn::make('department')
                    ->label('القسم')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'computer_science' => 'علوم الحاسوب',
                        'mathematics' => 'الرياضيات',
                        'physics' => 'الفيزياء',
                        'chemistry' => 'الكيمياء',
                        'biology' => 'الأحياء',
                        'english' => 'اللغة الإنجليزية',
                        'arabic' => 'اللغة العربية',
                        'history' => 'التاريخ',
                        'geography' => 'الجغرافيا',
                        'islamic_studies' => 'الدراسات الإسلامية',
                        'other' => 'أخرى',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'computer_science',
                        'success' => 'mathematics',
                        'warning' => 'physics',
                        'danger' => 'chemistry',
                        'info' => 'biology',
                        'secondary' => ['english', 'arabic'],
                        'gray' => 'other',
                    ]),

                BadgeColumn::make('qualification')
                    ->label('المؤهل العلمي')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bachelor' => 'بكالوريوس',
                        'master' => 'ماجستير',
                        'phd' => 'دكتوراه',
                        'diploma' => 'دبلوم',
                        'other' => 'أخرى',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'phd',
                        'warning' => 'master',
                        'primary' => 'bachelor',
                        'secondary' => 'diploma',
                        'gray' => 'other',
                    ]),

                BadgeColumn::make('employment_status')
                    ->label('حالة التوظيف')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'on_leave' => 'في إجازة',
                        'terminated' => 'منتهي الخدمة',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'active',
                        'warning' => 'on_leave',
                        'danger' => ['inactive', 'terminated'],
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-clock' => 'on_leave',
                        'heroicon-o-x-circle' => ['inactive', 'terminated'],
                    ]),

                TextColumn::make('specialization')
                    ->label('التخصص')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('hire_date')
                    ->label('تاريخ التوظيف')
                    ->date('Y-m-d')
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
                SelectFilter::make('employment_status')
                    ->label('حالة التوظيف')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'on_leave' => 'في إجازة',
                        'terminated' => 'منتهي الخدمة',
                    ]),

                SelectFilter::make('department')
                    ->label('القسم')
                    ->options([
                        'computer_science' => 'علوم الحاسوب',
                        'mathematics' => 'الرياضيات',
                        'physics' => 'الفيزياء',
                        'chemistry' => 'الكيمياء',
                        'biology' => 'الأحياء',
                        'english' => 'اللغة الإنجليزية',
                        'arabic' => 'اللغة العربية',
                        'history' => 'التاريخ',
                        'geography' => 'الجغرافيا',
                        'islamic_studies' => 'الدراسات الإسلامية',
                        'other' => 'أخرى',
                    ]),

                SelectFilter::make('qualification')
                    ->label('المؤهل العلمي')
                    ->options([
                        'bachelor' => 'بكالوريوس',
                        'master' => 'ماجستير',
                        'phd' => 'دكتوراه',
                        'diploma' => 'دبلوم',
                        'other' => 'أخرى',
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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
