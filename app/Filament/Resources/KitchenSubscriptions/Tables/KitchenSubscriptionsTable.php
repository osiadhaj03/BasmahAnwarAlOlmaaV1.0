<?php

namespace App\Filament\Resources\KitchenSubscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KitchenSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('المشترك')  
                    ->searchable(),
                TextColumn::make('kitchen.name')
                    ->label('المطبخ')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('monthly_price')
                    ->label('قيمة الاشتراك الشهري')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('number_meal')
                    ->label('عدد الوجبات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
