<?php

namespace App\Filament\Resources\KitchenExpenses\Pages;

use App\Filament\Resources\KitchenExpenses\KitchenExpenseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKitchenExpense extends EditRecord
{
    protected static string $resource = KitchenExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
