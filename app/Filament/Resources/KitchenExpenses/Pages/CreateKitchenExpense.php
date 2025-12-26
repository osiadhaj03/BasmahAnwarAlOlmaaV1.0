<?php

namespace App\Filament\Resources\KitchenExpenses\Pages;

use App\Filament\Resources\KitchenExpenses\KitchenExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKitchenExpense extends CreateRecord
{
    protected static string $resource = KitchenExpenseResource::class;

    /**
     * تعيين created_by تلقائياً للمستخدم الحالي قبل الإنشاء
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        
        return $data;
    }
}
