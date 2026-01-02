<?php

namespace App\Filament\Resources\KitchenInvoices\Pages;

use App\Filament\Resources\KitchenInvoices\KitchenInvoiceResource;
use App\Models\KitchenSubscription;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKitchenInvoice extends EditRecord
{
    protected static string $resource = KitchenInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * تحميل بيانات إضافية عند فتح النموذج للتعديل
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // إضافة رقم الاشتراك للعرض
        if (isset($data['subscription_id'])) {
            $subscription = KitchenSubscription::find($data['subscription_id']);
            $data['subscription_number_display'] = $subscription?->subscription_number ?? 'بدون رقم';
        }
        
        return $data;
    }
}
