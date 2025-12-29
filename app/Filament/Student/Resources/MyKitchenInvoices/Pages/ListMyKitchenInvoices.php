<?php

namespace App\Filament\Student\Resources\MyKitchenInvoices\Pages;

use App\Filament\Student\Resources\MyKitchenInvoices\MyKitchenInvoicesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyKitchenInvoices extends ListRecords
{
    protected static string $resource = MyKitchenInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
