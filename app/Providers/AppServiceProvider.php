<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Attendance;
use App\Models\KitchenPayment;
use App\Observers\AttendanceObserver;
use App\Observers\KitchenPaymentObserver;

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
        
        // تسجيل Observer للدفعات - لتحديث حالة الفواتير تلقائياً
        KitchenPayment::observe(KitchenPaymentObserver::class);
    }
}

