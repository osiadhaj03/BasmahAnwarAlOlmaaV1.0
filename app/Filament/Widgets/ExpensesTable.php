<?php

namespace App\Filament\Widgets;

use App\Models\KitchenExpense;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class ExpensesTable extends BaseWidget
{
    protected static ?int $sort = 6;
    protected static ?string $heading = 'مصاريف هذا الشهر';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return $table
            ->query(
                KitchenExpense::query()
                    ->whereMonth('expense_date', $currentMonth)
                    ->whereYear('expense_date', $currentYear)
                    ->with(['category', 'creator', 'supplier'])
                    ->latest('expense_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('JOD'),
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('المنشئ')
                    ->placeholder('-'),
            ])
            ->defaultSort('expense_date', 'desc')
            ->paginated([5, 10, 25]);
    }
}
