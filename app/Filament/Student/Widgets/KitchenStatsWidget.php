<?php

namespace App\Filament\Student\Widgets;

use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\MealDelivery;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class KitchenStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = Auth::id();
        
        // جلب الاشتراك الفعال
        $subscription = KitchenSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->first();
        
        if (!$subscription) {
            return [
                Stat::make('لا يوجد اشتراك', 'غير مشترك')
                    ->description('اشترك في خدمة المطبخ')
                    ->color('warning')
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        }

        // حساب الرصيد (المستحق) = مجموع الفواتير غير المدفوعة
        $totalOutstanding = KitchenInvoice::where('user_id', $userId)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->get()
            ->sum(fn ($inv) => $inv->remaining_amount);

        // عدد الوجبات الفائتة (missed)
        $missedMeals = MealDelivery::where('user_id', $userId)
            ->where('status', 'missed')
            ->count();

        // عدد الوجبات المستلمة هذا الشهر
        $deliveredThisMonth = MealDelivery::where('subscription_id', $subscription->id)
            ->where('status', 'delivered')
            ->whereMonth('delivery_date', now()->month)
            ->whereYear('delivery_date', now()->year)
            ->count();

        return [
            Stat::make('المبلغ المستحق', number_format($totalOutstanding, 2) . ' د.أ')
                ->description($totalOutstanding > 0 ? 'يرجى السداد' : 'لا يوجد مستحقات')
                ->color($totalOutstanding > 0 ? 'danger' : 'success')
                ->icon($totalOutstanding > 0 ? 'heroicon-o-banknotes' : 'heroicon-o-check-circle'),

            Stat::make('وجبات هذا الشهر', $deliveredThisMonth)
                ->description('وجبة مستلمة')
                ->color('success')
                ->icon('heroicon-o-cake'),

            Stat::make('الوجبات الفائتة', $missedMeals)
                ->description('وجبة لم تُستلم')
                ->color($missedMeals > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
