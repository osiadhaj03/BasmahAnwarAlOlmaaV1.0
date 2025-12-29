<?php

namespace App\Observers;

use App\Models\KitchenInvoice;
use App\Models\KitchenPayment;
use App\Models\KitchenSubscription;

class KitchenInvoiceObserver
{
    /**
     * Handle the KitchenInvoice "created" event.
     * عند إنشاء فاتورة جديدة - خصم من رصيد المحفظة إذا كان متاحاً
     */
    public function created(KitchenInvoice $invoice): void
    {
        $this->applyCredit($invoice);
    }

    /**
     * تطبيق الرصيد المتاح على الفاتورة الجديدة
     */
    protected function applyCredit(KitchenInvoice $invoice): void
    {
        // التأكد من وجود اشتراك
        if (!$invoice->subscription_id) {
            return;
        }

        $subscription = KitchenSubscription::find($invoice->subscription_id);
        if (!$subscription) {
            return;
        }

        // التحقق من وجود رصيد متاح
        $availableCredit = $subscription->available_credit;
        if ($availableCredit <= 0) {
            return;
        }

        // حساب المبلغ الذي سيتم خصمه (الأقل بين الرصيد ومبلغ الفاتورة)
        $amountToApply = min($availableCredit, $invoice->amount);

        // خصم من الرصيد
        $subscription->deductCredit($amountToApply);

        // إنشاء دفعة تلقائية من الرصيد
        KitchenPayment::create([
            'invoice_id' => $invoice->id,
            'subscription_id' => $invoice->subscription_id,
            'amount' => $amountToApply,
            'payment_date' => now(),
            'payment_method' => 'credit_balance', // من رصيد المحفظة
            'collected_by' => auth()->id(),
            'notes' => 'خصم تلقائي من رصيد المحفظة',
        ]);
    }
}
