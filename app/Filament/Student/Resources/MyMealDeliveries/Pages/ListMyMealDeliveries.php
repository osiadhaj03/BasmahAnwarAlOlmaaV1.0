<?php

namespace App\Filament\Student\Resources\MyMealDeliveries\Pages;

use App\Filament\Student\Resources\MyMealDeliveries\MyMealDeliveriesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyMealDeliveries extends ListRecords
{
    protected static string $resource = MyMealDeliveriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
