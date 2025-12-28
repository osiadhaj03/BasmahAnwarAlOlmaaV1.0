<?php

namespace App\Filament\Student\Resources\MyDiplomas\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MyDiplomasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الدبلوم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),

                TextColumn::make('lessons_count')
                    ->label('عدد الدورات')
                    ->counts('lessons')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->date('Y-m-d')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->paginated([10, 25]);
    }
}
