<?php

namespace App\Filament\Student\Resources\MyKitchenInvoices\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MyKitchenInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('jod')
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->label('المدفوع')
                    ->money('jod')
                    ->color('success'),
                TextColumn::make('remaining_amount')
                    ->label('المتبقي')
                    ->money('jod')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (string $state, $record): string => $record->status_arabic)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'pending' => 'gray',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('billing_date')
                    ->label('تاريخ الفاتورة')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),
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
