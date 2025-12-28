<?php

namespace App\Filament\Widgets;

use App\Models\KitchenSubscription;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSubscriptionsTable extends BaseWidget
{
    protected static ?string $heading = 'آخر الاشتراكات';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                KitchenSubscription::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم المشترك'),
                Tables\Columns\TextColumn::make('kitchen.name')
                    ->label('المطبخ'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->date(),
                Tables\Columns\TextColumn::make('monthly_price')
                    ->label('السعر الشهري')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'paused' => 'موقوف',
                        'cancelled' => 'ملغي',
                        default => $state,
                    }),
            ])
            ->paginated(false);
    }
}
