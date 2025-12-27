<?php

namespace App\Filament\Cook\Resources\KitchenSubscriptions\Pages;

use App\Filament\Cook\Resources\KitchenSubscriptions\KitchenSubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKitchenSubscription extends EditRecord
{
    protected static string $resource = KitchenSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
