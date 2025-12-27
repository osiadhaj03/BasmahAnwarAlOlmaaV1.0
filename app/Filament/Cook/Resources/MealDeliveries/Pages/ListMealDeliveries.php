<?php

namespace App\Filament\Cook\Resources\MealDeliveries\Pages;

use App\Filament\Cook\Resources\MealDeliveries\MealDeliveryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMealDeliveries extends ListRecords
{
    protected static string $resource = MealDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
