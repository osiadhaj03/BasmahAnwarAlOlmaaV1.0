<?php

namespace App\Filament\Cook\Resources\KitchenSubscriptions\Pages;

use App\Filament\Cook\Resources\KitchenSubscriptions\KitchenSubscriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKitchenSubscriptions extends ListRecords
{
    protected static string $resource = KitchenSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
