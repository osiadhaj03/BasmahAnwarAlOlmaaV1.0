<?php

namespace App\Filament\Cook\Resources\KitchenExpenses\Pages;

use App\Filament\Cook\Resources\KitchenExpenses\KitchenExpensesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKitchenExpenses extends EditRecord
{
    protected static string $resource = KitchenExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
