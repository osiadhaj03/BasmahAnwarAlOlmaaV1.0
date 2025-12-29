<?php

namespace App\Observers;

use App\Models\KitchenInvoice;
use App\Models\KitchenPayment;
use App\Models\KitchenSubscription;

class KitchenInvoiceObserver
{
    /**
     * Handle the KitchenInvoice "created" event.
     * عند إنشاء فاتورة جديدة - تطبيق الرصيد المتاح تلقائياً
     */
    public function created(KitchenInvoice $invoice): void
    {
        $this->applyAvailableCredit($invoice);
    }

    /**
     * تطبيق الرصيد المتاح على الفاتورة الجديدة
     * الرصيد = مجموع الدفعات - مجموع الفواتير (قبل هذه الفاتورة)
     */
    protected function applyAvailableCredit(KitchenInvoice $invoice): void
    {
        // التأكد من وجود اشتراك
        if (!$invoice->subscription_id) {
            return;
        }

        $subscription = KitchenSubscription::find($invoice->subscription_id);
        if (!$subscription) {
            return;
        }

        // حساب الرصيد المتاح ديناميكياً
        // ملاحظة: الفاتورة الجديدة تم إنشاؤها الآن، لذا نحتاج حساب الرصيد بدونها
        $totalPayments = $subscription->payments()->sum('amount');
        $totalInvoicesExceptThis = $subscription->invoices()
            ->where('id', '!=', $invoice->id)
            ->sum('amount');
        
        $availableCredit = $totalPayments - $totalInvoicesExceptThis;

        // إذا لم يوجد رصيد متاح
        if ($availableCredit <= 0) {
            return;
        }

        // حساب المبلغ الذي سيتم خصمه (الأقل بين الرصيد ومبلغ الفاتورة)
        $amountToApply = min($availableCredit, $invoice->amount);

        // إنشاء دفعة تلقائية من الرصيد
        KitchenPayment::create([
            'invoice_id' => $invoice->id,
            'subscription_id' => $invoice->subscription_id,
            'amount' => $amountToApply,
            'payment_date' => now(),
            'payment_method' => 'credit_balance', // من الرصيد المتاح
            'collected_by' => auth()->id(),
            'notes' => 'خصم تلقائي من الرصيد المتاح',
        ]);
    }
}
