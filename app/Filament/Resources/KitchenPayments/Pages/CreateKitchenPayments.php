<?php

namespace App\Filament\Resources\KitchenPayments\Pages;

use App\Filament\Resources\KitchenPayments\KitchenPaymentsResource;
use App\Models\KitchenInvoice;
use App\Models\KitchenPayment;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateKitchenPayments extends CreateRecord
{
    protected static string $resource = KitchenPaymentsResource::class;

    /**
     * التحقق من البيانات قبل الإنشاء
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $subscriptionId = $data['subscription_id'] ?? null;
        $amount = (float) ($data['amount'] ?? 0);

        // حساب إجمالي المستحق
        $totalOutstanding = KitchenPayment::getTotalOutstandingForSubscription($subscriptionId);

        // التحقق من عدم دفع أكثر من المستحق
        if ($amount > $totalOutstanding) {
            Notification::make()
                ->title('خطأ في المبلغ')
                ->body('لا يمكن دفع مبلغ (' . number_format($amount, 2) . ' د.أ) أكبر من إجمالي المستحق (' . number_format($totalOutstanding, 2) . ' د.أ)')
                ->danger()
                ->send();
            
            $this->halt();
        }

        // إذا لا يوجد مستحق
        if ($totalOutstanding <= 0) {
            Notification::make()
                ->title('لا توجد فواتير مستحقة')
                ->body('هذا المشترك ليس عليه أي فواتير مستحقة للدفع.')
                ->warning()
                ->send();
            
            $this->halt();
        }

        // تنظيف البيانات من الحقول غير المخزنة
        unset($data['user_id_selector']);
        unset($data['subscription_number_display']);
        unset($data['credit_balance_display']);
        unset($data['total_selected_display']);
        unset($data['selected_invoices']);
        unset($data['invoice_id']); // لم نعد نحتاجه

        return $data;
    }

    /**
     * معالجة إنشاء الدفعة وتوزيعها على الفواتير
     */
    protected function handleRecordCreation(array $data): Model
    {
        // إنشاء الدفعة
        $payment = static::getModel()::create($data);

        // توزيع الدفعة على الفواتير بنظام FIFO
        $payment->allocateToInvoices();

        // إشعار بنجاح العملية
        Notification::make()
            ->title('تم تسجيل الدفعة بنجاح')
            ->body('تم توزيع المبلغ (' . number_format($data['amount'], 2) . ' د.أ) على الفواتير المستحقة.')
            ->success()
            ->send();

        return $payment;
    }
}

