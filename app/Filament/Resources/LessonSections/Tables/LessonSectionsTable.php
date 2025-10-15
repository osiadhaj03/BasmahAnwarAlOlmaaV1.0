<?php

namespace App\Filament\Resources\LessonSections\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LessonSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable()
                    ->width(80)
                    ->alignCenter(),
                
                TextColumn::make('name')
                    ->label('اسم القسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                
                ColorColumn::make('color')
                    ->label('اللون')
                    ->alignCenter()
                    ->width(80),
                
                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->width(80),
                
                TextColumn::make('lessons_count')
                    ->label('عدد الدروس')
                    ->counts('lessons')
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->width(100),
                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('جميع الأقسام')
                    ->trueLabel('الأقسام النشطة')
                    ->falseLabel('الأقسام غير النشطة'),
                
                SelectFilter::make('has_lessons')
                    ->label('حسب الدروس')
                    ->options([
                        'with_lessons' => 'أقسام تحتوي على دروس',
                        'without_lessons' => 'أقسام فارغة',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'with_lessons') {
                            return $query->has('lessons');
                        } elseif ($data['value'] === 'without_lessons') {
                            return $query->doesntHave('lessons');
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض'),
                EditAction::make()
                    ->label('تعديل'),
                Action::make('toggle_active')
                    ->label(fn ($record) => $record->is_active ? 'إلغاء التفعيل' : 'تفعيل')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_active ? 'إلغاء تفعيل القسم' : 'تفعيل القسم')
                    ->modalDescription(fn ($record) => $record->is_active 
                        ? 'هل أنت متأكد من إلغاء تفعيل هذا القسم؟ سيصبح غير مرئي في قائمة الأقسام النشطة.'
                        : 'هل أنت متأكد من تفعيل هذا القسم؟ سيصبح مرئياً في قائمة الأقسام النشطة.'
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->striped()
            ->emptyStateHeading('لا توجد أقسام دروس')
            ->emptyStateDescription('ابدأ بإنشاء قسم جديد لتنظيم دروسك')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
