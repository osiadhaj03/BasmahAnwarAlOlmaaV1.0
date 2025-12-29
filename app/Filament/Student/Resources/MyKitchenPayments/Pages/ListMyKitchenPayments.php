<?php

namespace App\Filament\Student\Resources\MyKitchenPayments\Pages;

use App\Filament\Student\Resources\MyKitchenPayments\MyKitchenPaymentsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyKitchenPayments extends ListRecords
{
    protected static string $resource = MyKitchenPaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
