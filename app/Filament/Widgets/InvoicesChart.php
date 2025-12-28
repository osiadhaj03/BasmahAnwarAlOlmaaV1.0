<?php

namespace App\Filament\Widgets;

use App\Models\KitchenInvoice;
use Filament\Widgets\ChartWidget;

class InvoicesChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected ?string $heading = 'الإيرادات الشهرية';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // I need to check if Flowframe/Trend is installed. 
        // If not, I will use a manual query. 
        // Looking at the previous conversation, I don't see it explicitly.
        // Let's assume standard Eloquent for now to be safe, or check composer.json.
        
        $data = KitchenInvoice::paid()
            ->selectRaw('SUM(amount) as sum, DATE_FORMAT(billing_date, "%Y-%m") as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (ر.س)',
                    'data' => $data->map(fn ($value) => $value->sum)->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $data->map(fn ($value) => $value->month)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
