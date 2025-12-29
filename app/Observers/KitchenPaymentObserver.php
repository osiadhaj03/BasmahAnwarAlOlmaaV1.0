<?php

namespace App\Observers;

use App\Models\KitchenPayment;
use App\Models\KitchenInvoice;

class KitchenPaymentObserver
{
    /**
     * Handle the KitchenPayment "created" event.
     * تحديث حالة الفاتورة تلقائياً عند إضافة دفعة جديدة
     */
    public function created(KitchenPayment $payment): void
    {
        $this->updateInvoiceStatus($payment);
    }

    /**
     * Handle the KitchenPayment "updated" event.
     * تحديث حالة الفاتورة عند تعديل دفعة
     */
    public function updated(KitchenPayment $payment): void
    {
        $this->updateInvoiceStatus($payment);
    }

    /**
     * Handle the KitchenPayment "deleted" event.
     * تحديث حالة الفاتورة عند حذف دفعة
     */
    public function deleted(KitchenPayment $payment): void
    {
        $this->updateInvoiceStatus($payment);
    }

    /**
     * تحديث حالة الفاتورة بناءً على مجموع الدفعات
     */
    protected function updateInvoiceStatus(KitchenPayment $payment): void
    {
        // إذا كانت الدفعة مرتبطة بفاتورة
        if ($payment->invoice_id) {
            $invoice = KitchenInvoice::find($payment->invoice_id);
            
            if ($invoice) {
                // حساب المجموع المدفوع
                $totalPaid = $invoice->payments()->sum('amount');
                
                // تحديد الحالة بناءً على المبلغ المدفوع
                if ($totalPaid >= $invoice->amount) {
                    // مدفوعة بالكامل
                    $invoice->status = 'paid';
                    $invoice->paid_at = now();
                } elseif ($totalPaid > 0) {
                    // مدفوعة جزئياً
                    $invoice->status = 'partial';
                } else {
                    // التحقق إذا كانت متأخرة
                    if ($invoice->due_date < today()) {
                        $invoice->status = 'overdue';
                    } else {
                        $invoice->status = 'pending';
                    }
                }
                
                $invoice->save();
            }
        }
    }
}
