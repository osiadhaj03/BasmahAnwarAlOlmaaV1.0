<?php

namespace App\Filament\Resources\KitchenPayments\Pages;

use App\Filament\Resources\KitchenPayments\KitchenPaymentsResource;
use App\Models\KitchenInvoice;
use App\Models\KitchenPayment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateKitchenPayments extends CreateRecord
{
    protected static string $resource = KitchenPaymentsResource::class;

    /**
     * معالجة إنشاء الدفعات للفواتير المتعددة
     */
    protected function handleRecordCreation(array $data): Model
    {
        $selectedInvoices = $data['selected_invoices'] ?? [];
        $totalAmount = (float) ($data['amount'] ?? 0);
        $subscriptionId = $data['subscription_id'] ?? null;
        
        // إذا لم يتم اختيار فواتير أو المبلغ صفر
        if (empty($selectedInvoices) || $totalAmount <= 0) {
            // إنشاء دفعة واحدة عادية
            return static::getModel()::create($data);
        }

        // جلب الفواتير المختارة مرتبة حسب الأقدم
        $invoices = KitchenInvoice::whereIn('id', $selectedInvoices)
            ->orderBy('due_date', 'asc')
            ->get();

        $remainingAmount = $totalAmount;
        $createdPayments = [];
        $firstPayment = null;

        foreach ($invoices as $invoice) {
            if ($remainingAmount <= 0) {
                break;
            }

            $invoiceRemaining = $invoice->remaining_amount;
            $amountForThisInvoice = min($remainingAmount, $invoiceRemaining);

            // إنشاء دفعة لهذه الفاتورة
            $paymentData = [
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscriptionId,
                'amount' => $amountForThisInvoice,
                'payment_date' => $data['payment_date'] ?? now(),
                'payment_method' => $data['payment_method'] ?? 'cash',
                'collected_by' => $data['collected_by'] ?? auth()->id(),
                'notes' => $data['notes'] ?? null,
            ];

            $payment = KitchenPayment::create($paymentData);
            $createdPayments[] = $payment;

            if (!$firstPayment) {
                $firstPayment = $payment;
            }

            $remainingAmount -= $amountForThisInvoice;
        }

        // إذا بقي مبلغ زائد، سيتم إضافته للرصيد تلقائياً من Observer

        // إرجاع الدفعة الأولى (للعرض في Filament)
        return $firstPayment ?? static::getModel()::create($data);
    }

    /**
     * تعديل البيانات قبل الإنشاء
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // تعيين أول فاتورة في invoice_id للتوافق
        if (!empty($data['selected_invoices'])) {
            $data['invoice_id'] = $data['selected_invoices'][0];
        }
        
        // إزالة الحقول غير الموجودة في الجدول
        unset($data['user_id_selector']);
        unset($data['subscription_number_display']);
        unset($data['credit_balance_display']);
        unset($data['total_selected_display']);
        unset($data['selected_invoices']);
        
        return $data;
    }
}
