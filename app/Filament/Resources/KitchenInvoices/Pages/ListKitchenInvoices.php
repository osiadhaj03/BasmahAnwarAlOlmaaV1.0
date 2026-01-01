<?php

namespace App\Filament\Resources\KitchenInvoices\Pages;

use App\Filament\Resources\KitchenInvoices\KitchenInvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListKitchenInvoices extends ListRecords
{
    protected static string $resource = KitchenInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('autoInvoice')
                ->label('📊 حساب الفواتير التلقائي')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->url(route('filament.admin.pages.auto-invoice-calculator')),
            CreateAction::make(),
        ];
    }
}


