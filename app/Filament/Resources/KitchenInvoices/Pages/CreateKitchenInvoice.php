<?php

namespace App\Filament\Resources\KitchenInvoices\Pages;

use App\Filament\Resources\KitchenInvoices\KitchenInvoiceResource;
use App\Models\KitchenInvoice;
use Filament\Resources\Pages\CreateRecord;

class CreateKitchenInvoice extends CreateRecord
{
    protected static string $resource = KitchenInvoiceResource::class;

    /**
     * توليد رقم الفاتورة عند الحفظ لتجنب التكرار
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // توليد رقم فاتورة جديد عند الحفظ (وليس عند فتح النموذج)
        $data['invoice_number'] = KitchenInvoice::generateInvoiceNumber();
        
        // إزالة الحقول غير الموجودة في الجدول
        unset($data['user_id_selector']);
        unset($data['subscription_number_display']);
        
        return $data;
    }
}
