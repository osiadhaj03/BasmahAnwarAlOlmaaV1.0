<?php

namespace App\Filament\Resources\LessonSectionStudents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\LessonSection;

class LessonSectionStudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->student->email ?? ''),
                
                TextColumn::make('student.student_id')
                    ->label('رقم الطالب')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('lessonSection.name')
                    ->label('قسم الدورة')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->lessonSection->description ?? ''),
                
                TextColumn::make('enrolled_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('enrollment_status_arabic')
                    ->label('حالة التسجيل')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'نشط' => 'success',
                        'متوقف' => 'warning',
                        'مكتمل' => 'gray',
                        default => 'gray',
                    }),
                
                TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('lesson_section_id')
                    ->label('قسم الدورة')
                    ->relationship('lessonSection', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('enrollment_status')
                    ->label('حالة التسجيل')
                    ->options([
                        'active' => 'نشط',
                        'dropped' => 'متوقف',
                        'completed' => 'مكتمل',
                    ]),
                
                Filter::make('enrolled_this_month')
                    ->label('المسجلين هذا الشهر')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('enrolled_at', now()->month))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض'),
                EditAction::make()
                    ->label('تعديل'),
                Action::make('toggle_status')
                    ->label('تغيير الحالة')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function ($record) {
                        $newStatus = match ($record->enrollment_status) {
                            'active' => 'dropped',
                            'dropped' => 'active',
                            'completed' => 'active',
                            default => 'active',
                        };
                        $record->update(['enrollment_status' => $newStatus]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('تغيير حالة التسجيل')
                    ->modalDescription('هل أنت متأكد من تغيير حالة تسجيل هذا الطالب؟'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('enrolled_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
