<?php

namespace App\Filament\Resources\KitchenSubscriptions\Pages;

use App\Filament\Resources\KitchenSubscriptions\KitchenSubscriptionResource;
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
