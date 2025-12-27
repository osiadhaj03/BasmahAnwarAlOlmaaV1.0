<?php

namespace App\Filament\Cook\Resources\KitchenPayments\Pages;

use App\Filament\Cook\Resources\KitchenPayments\KitchenPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKitchenPayment extends EditRecord
{
    protected static string $resource = KitchenPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
