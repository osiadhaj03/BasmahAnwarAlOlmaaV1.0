<?php

namespace App\Filament\Resources\MealDeliveries\Pages;

use App\Filament\Resources\MealDeliveries\MealDeliveryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMealDelivery extends EditRecord
{
    protected static string $resource = MealDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
