<?php

namespace App\Observers;

use App\Models\KitchenPayment;
use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;

class KitchenPaymentObserver
{
    /**
     * Handle the KitchenPayment "created" event.
     * تحديث حالة الفاتورة وإدارة الرصيد الزائد
     */
    public function created(KitchenPayment $payment): void
    {
        $this->processPayment($payment);
    }

    /**
     * Handle the KitchenPayment "updated" event.
     */
    public function updated(KitchenPayment $payment): void
    {
        $this->updateInvoiceStatus($payment);
    }

    /**
     * Handle the KitchenPayment "deleted" event.
     */
    public function deleted(KitchenPayment $payment): void
    {
        $this->updateInvoiceStatus($payment);
    }

    /**
     * معالجة الدفعة الجديدة
     * - تحديث حالة الفاتورة
     * - إضافة الزائد للرصيد
     */
    protected function processPayment(KitchenPayment $payment): void
    {
        if (!$payment->invoice_id) {
            return;
        }

        $invoice = KitchenInvoice::find($payment->invoice_id);
        if (!$invoice) {
            return;
        }

        // حساب المجموع المدفوع
        $totalPaid = $invoice->payments()->sum('amount');
        $invoiceAmount = $invoice->amount;

        // حساب الفائض
        $excess = $totalPaid - $invoiceAmount;

        if ($totalPaid >= $invoiceAmount) {
            // الفاتورة مدفوعة بالكامل
            $invoice->status = 'paid';
            $invoice->paid_at = now();
            $invoice->save();

            // إذا كان هناك فائض، أضفه لرصيد الاشتراك
            if ($excess > 0 && $payment->subscription_id) {
                $subscription = KitchenSubscription::find($payment->subscription_id);
                if ($subscription) {
                    $subscription->addCredit($excess);
                }
            }
        } elseif ($totalPaid > 0) {
            // مدفوعة جزئياً
            $invoice->status = 'partial';
            $invoice->save();
        } else {
            // التحقق إذا كانت متأخرة
            if ($invoice->due_date < today()) {
                $invoice->status = 'overdue';
            } else {
                $invoice->status = 'pending';
            }
            $invoice->save();
        }
    }

    /**
     * تحديث حالة الفاتورة فقط (للتعديل والحذف)
     */
    protected function updateInvoiceStatus(KitchenPayment $payment): void
    {
        if (!$payment->invoice_id) {
            return;
        }

        $invoice = KitchenInvoice::find($payment->invoice_id);
        if (!$invoice) {
            return;
        }

        // حساب المجموع المدفوع
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid >= $invoice->amount) {
            $invoice->status = 'paid';
            $invoice->paid_at = now();
        } elseif ($totalPaid > 0) {
            $invoice->status = 'partial';
        } else {
            if ($invoice->due_date < today()) {
                $invoice->status = 'overdue';
            } else {
                $invoice->status = 'pending';
            }
        }
        
        $invoice->save();
    }
}
