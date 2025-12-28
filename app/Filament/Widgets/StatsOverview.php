<?php

namespace App\Filament\Widgets;

use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الطلاب', User::count())
                ->description('إجمالي المستخدمين المسجلين')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('الاشتراكات النشطة', KitchenSubscription::active()->count())
                ->description('اشتراكات المطبخ النشطة حالياً')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),
            Stat::make('إجمالي الإيرادات', KitchenInvoice::paid()->sum('amount') . ' ر.س')
                ->description('إجمالي المبالغ المحصلة من الفواتير المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
