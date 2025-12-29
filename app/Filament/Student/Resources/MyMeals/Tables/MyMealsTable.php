<?php

namespace App\Filament\Student\Resources\MyMeals\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class MyMealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular(),
                TextColumn::make('name')
                    ->label('اسم الوجبة')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description),
                TextColumn::make('meal_type_arabic')
                    ->label('نوع الوجبة')
                    ->badge(),
                TextColumn::make('meal_date')
                    ->label('تاريخ الوجبة')
                    ->date()
                    ->sortable(),
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
