<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Attendance;
use App\Models\KitchenPayment;
use App\Models\KitchenInvoice;
use App\Observers\AttendanceObserver;
use App\Observers\KitchenPaymentObserver;
use App\Observers\KitchenInvoiceObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تسجيل Observer للحضور
        Attendance::observe(AttendanceObserver::class);
        
        // تسجيل Observer للدفعات - لتحديث حالة الفواتير وإضافة الفائض للرصيد
        KitchenPayment::observe(KitchenPaymentObserver::class);
        
        // تسجيل Observer للفواتير - لخصم الرصيد تلقائياً عند إنشاء فاتورة
        KitchenInvoice::observe(KitchenInvoiceObserver::class);
    }
}


