<?php

namespace App\Filament\Student\Resources\MyKitchenPayments\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MyKitchenPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('sar')
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method_arabic')
                    ->label('طريقة الدفع'),
                TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50),
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
