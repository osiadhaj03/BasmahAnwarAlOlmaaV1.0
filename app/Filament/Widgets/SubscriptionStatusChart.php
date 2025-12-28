<?php

namespace App\Filament\Widgets;

use App\Models\KitchenSubscription;
use Filament\Widgets\ChartWidget;

class SubscriptionStatusChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'توزيع حالات الاشتراكات';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $active = KitchenSubscription::active()->count();
        $paused = KitchenSubscription::paused()->count();
        $cancelled = KitchenSubscription::cancelled()->count();

        return [
            'datasets' => [
                [
                    'label' => 'الاشتراكات',
                    'data' => [$active, $paused, $cancelled],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => ['نشط', 'موقوف', 'ملغي'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
