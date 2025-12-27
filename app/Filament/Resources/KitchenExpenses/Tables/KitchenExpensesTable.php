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
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expense_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('creator_name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('kitchen.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
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
