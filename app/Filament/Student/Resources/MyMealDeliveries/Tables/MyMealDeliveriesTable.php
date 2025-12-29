<?php

namespace App\Filament\Student\Resources\MyMealDeliveries\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn; // Fallback if TextColumn::badge() is not available in this version, but strictly following user preference for TextColumn::badge if possible, though user mentioned deprecated BadgeColumn update in history. Let's use TextColumn with badge() or formatStateUsing for status.

class MyMealDeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('meal.name')
                    ->label('الوجبة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('delivery_date')
                    ->label('تاريخ التوصيل')
                    ->date()
                    ->sortable(),
                TextColumn::make('meal_type_arabic')
                    ->label('نوع الوجبة'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (string $state, $record): string => $record->status_arabic)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'delivered' => 'success',
                        'pending' => 'warning',
                        'missed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('delivered_at')
                    ->label('وقت الاستلام')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
