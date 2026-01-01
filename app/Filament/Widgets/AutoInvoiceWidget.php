<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AutoInvoiceWidget extends Widget
{
    protected string $view = 'filament.widgets.auto-invoice-widget';
    
    protected int | string | array $columnSpan = 'full';
}
