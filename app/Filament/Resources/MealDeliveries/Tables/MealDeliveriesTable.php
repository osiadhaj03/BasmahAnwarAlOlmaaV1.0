<?php

namespace App\Filament\Resources\MealDeliveries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MealDeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('المشترك')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('delivery_date')
                    ->label('تاريخ التسليم')
                    ->date()
                    ->sortable(),
                TextColumn::make('meal.name')
                    ->label('الوجبة')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'قيد الانتظار',
                        'delivered' => 'تم التسليم',
                        'missed' => 'فائت',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'delivered' => 'success',
                        'missed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('deliveredBy.name')
                    ->label('المُسلّم')
                    ->searchable(),
                TextColumn::make('delivered_at')
                    ->label('وقت التسليم')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'delivered' => 'تم التسليم',
                        'missed' => 'فائت',
                    ]),
                SelectFilter::make('meal_type')
                    ->label('نوع الوجبة')
                    ->options([
                        'breakfast' => 'فطور',
                        'lunch' => 'غداء',
                        'dinner' => 'عشاء',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('delivery_date', 'desc');
    }
}
