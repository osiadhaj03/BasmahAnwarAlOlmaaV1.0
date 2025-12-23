<?php

namespace App\Filament\Resources\KitchenExpenses\Pages;

use App\Filament\Resources\KitchenExpenses\KitchenExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKitchenExpenses extends ListRecords
{
    protected static string $resource = KitchenExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
