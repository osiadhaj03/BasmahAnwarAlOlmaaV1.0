<?php

namespace App\Filament\Student\Widgets;

use App\Models\KitchenSubscription;
use App\Models\MealDelivery;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentKitchenSubscriptionWidget extends Widget
{
    protected string $view = 'filament.student.widgets.student-kitchen-subscription-widget';
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $subscription = KitchenSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        // إذا لم يكن هناك اشتراك نشط، نبحث عن آخر اشتراك كيفما كانت حالته
        if (!$subscription) {
            $subscription = KitchenSubscription::where('user_id', $user->id)
                ->latest()
                ->first();
        }

        $stats = [];
        $todayMeal = null;

        if ($subscription) {
            $today = Carbon::today();
            
            // الوجبات المستلمة هذا الشهر
            $stats['monthly_meals'] = MealDelivery::where('subscription_id', $subscription->id)
                ->whereMonth('delivery_date', $today->month)
                ->where('status', 'delivered')
                ->count();
                
            // الوجبات المتبقية (تقريبي بناء على عدد أيام الشهر المتبقية)
            // يمكن تحسينها حسب منطق العمل في المطبخ
            
            // حالة وجبة اليوم
            $todayMeal = MealDelivery::where('subscription_id', $subscription->id)
                ->whereDate('delivery_date', $today)
                ->first();

            // الرصيد
            $stats['balance'] = $subscription->balance;
            
            // آخر فاتورة
            $stats['last_invoice'] = $subscription->invoices()->latest()->first();
        }

        return [
            'subscription' => $subscription,
            'stats' => $stats,
            'todayMeal' => $todayMeal,
        ];
    }
}
