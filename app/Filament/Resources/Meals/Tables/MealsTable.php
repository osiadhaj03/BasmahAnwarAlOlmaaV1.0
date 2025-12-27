<?php

namespace App\Filament\Resources\Meals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة'),
                TextColumn::make('name')
                    ->label('اسم الوجبة')
                    ->searchable(),
                TextColumn::make('meal_date')
                    ->label('تاريخ الوجبة')
                    ->date()
                    ->sortable(),
                TextColumn::make('kitchen.name')
                    ->label('المطبخ')
                    ->searchable(),

                TextColumn::make('meal_type')
                    ->label('نوع الوجبة')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'breakfast' => 'فطور',
                        'lunch' => 'غداء',
                        'dinner' => 'عشاء',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'breakfast' => 'warning',
                        'lunch' => 'success',
                        'dinner' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('meal_type')
                    ->label('نوع الوجبة')
                    ->options([
                        'breakfast' => 'فطور',
                        'lunch' => 'غداء',
                        'dinner' => 'عشاء',
                    ]),
                SelectFilter::make('kitchen_id')
                    ->label('المطبخ')
                    ->relationship('kitchen', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('meal_date', 'desc');
    }
}
