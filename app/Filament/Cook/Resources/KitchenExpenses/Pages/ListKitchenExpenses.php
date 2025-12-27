<?php

namespace App\Filament\Cook\Resources\KitchenExpenses\Pages;

use App\Filament\Cook\Resources\KitchenExpenses\KitchenExpensesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKitchenExpenses extends ListRecords
{
    protected static string $resource = KitchenExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
