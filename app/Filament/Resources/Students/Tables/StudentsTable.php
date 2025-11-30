<?php

namespace App\Filament\Resources\Students\Tables;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

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
                
                TextColumn::make('monthly_attendances_count')
                    ->label('الحضور هذا الشهر')
                    ->counts([
                        'attendances' => fn (Builder $query) => $query
                            ->where('status', 'present')
                            ->whereMonth('attendance_date', Carbon::now()->month)
                            ->whereYear('attendance_date', Carbon::now()->year),
                    ])
                    ->getStateUsing(function ($record) {
                        return $record->attendances()
                            ->where('status', 'present')
                            ->whereMonth('attendance_date', Carbon::now()->month)
                            ->whereYear('attendance_date', Carbon::now()->year)
                            ->count();
                    })
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->sortable(),
                
                
                
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
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير')
                    ->fileName('Students')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                    FilamentExportBulkAction::make('export')
                        ->label('تصدير المحدد')
                        ->fileName('Selected_Students')
                        ->defaultFormat('xlsx')
                        ->defaultPageOrientation('landscape'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
