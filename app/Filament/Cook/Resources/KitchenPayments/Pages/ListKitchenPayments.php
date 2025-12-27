<?php

namespace App\Filament\Cook\Resources\KitchenPayments\Pages;

use App\Filament\Cook\Resources\KitchenPayments\KitchenPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKitchenPayments extends ListRecords
{
    protected static string $resource = KitchenPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
