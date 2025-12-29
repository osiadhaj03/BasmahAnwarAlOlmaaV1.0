<?php

namespace App\Filament\Resources\KitchenExpenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KitchenExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expense_date')
                    ->label('التاريخ')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('creator_name')
                    ->label('المالك')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('kitchen.name')
                    ->label('المطبخ')
                    ->toggleable(),
                TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->label('القسم')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
