<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('student_id')
                    ->label('الرقم الجامعي')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('غير محدد'),
                
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-envelope'),
                
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-phone'),
                
                TextColumn::make('level')
                    ->label('المستوى الدراسي')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'freshman' => 'السنة الأولى',
                        'sophomore' => 'السنة الثانية',
                        'junior' => 'السنة الثالثة',
                        'senior' => 'السنة الرابعة',
                        'graduate' => 'دراسات عليا',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'freshman',
                        'success' => 'sophomore',
                        'warning' => 'junior',
                        'danger' => 'senior',
                        'info' => 'graduate',
                    ])
                    ->placeholder('غير محدد'),
                
                TextColumn::make('specialization')
                    ->label('التخصص')
                    ->searchable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-academic-cap'),
                
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'graduated' => 'متخرج',
                        'suspended' => 'موقوف',
                        'transferred' => 'محول',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'active',
                        'gray' => 'inactive',
                        'info' => 'graduated',
                        'danger' => 'suspended',
                        'warning' => 'transferred',
                    ]),
                
                TextColumn::make('date_of_birth')
                    ->label('تاريخ الميلاد')
                    ->date('Y-m-d')
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('emergency_contact_name')
                    ->label('جهة الاتصال في الطوارئ')
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
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
                SelectFilter::make('status')
                    ->label('حالة الطالب')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'graduated' => 'متخرج',
                        'suspended' => 'موقوف',
                        'transferred' => 'محول',
                    ]),
                
                SelectFilter::make('level')
                    ->label('المستوى الدراسي')
                    ->options([
                        'freshman' => 'السنة الأولى',
                        'sophomore' => 'السنة الثانية',
                        'junior' => 'السنة الثالثة',
                        'senior' => 'السنة الرابعة',
                        'graduate' => 'دراسات عليا',
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
