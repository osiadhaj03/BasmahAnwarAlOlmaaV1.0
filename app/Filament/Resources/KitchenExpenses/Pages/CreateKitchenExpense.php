<?php

namespace App\Filament\Resources\KitchenExpenses\Pages;

use App\Filament\Resources\KitchenExpenses\KitchenExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKitchenExpense extends CreateRecord
{
    protected static string $resource = KitchenExpenseResource::class;
}
