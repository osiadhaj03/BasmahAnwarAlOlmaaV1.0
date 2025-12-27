<?php

namespace App\Filament\Cook\Resources\KitchenInvoices\Pages;

use App\Filament\Cook\Resources\KitchenInvoices\KitchenInvoicesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKitchenInvoices extends ListRecords
{
    protected static string $resource = KitchenInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
