<?php

namespace App\Filament\Cook\Resources\KitchenInvoices\Pages;

use App\Filament\Cook\Resources\KitchenInvoices\KitchenInvoicesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKitchenInvoices extends EditRecord
{
    protected static string $resource = KitchenInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
