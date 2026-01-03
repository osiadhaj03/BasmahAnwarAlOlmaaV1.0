<?php

namespace App\Filament\Student\Widgets;

use App\Models\KitchenSubscription;
use App\Models\KitchenInvoice;
use App\Models\Kitchen;
use App\Models\MealDelivery;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentKitchenSubscriptionWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    
    protected string $view = 'filament.student.widgets.student-kitchen-subscription-widget';
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 3;

    /**
     * Action لطلب اشتراك جديد
     */
    public function requestSubscriptionAction(): Action
    {
        return Action::make('requestSubscription')
            ->label('طلب اشتراك جديد')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('تأكيد طلب الاشتراك')
            ->modalDescription('هل تود التأكيد على طلب اشتراك جديد في المطبخ مقابل 10 دنانير فقط لأول شهر')
            ->modalSubmitActionLabel('نعم، أريد الاشتراك')
            ->modalCancelActionLabel('إلغاء')
            ->action(function () {
                $user = Auth::user();
                
                // التحقق من عدم وجود اشتراك نشط
                $existingSubscription = KitchenSubscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();
                
                if ($existingSubscription) {
                    Notification::make()
                        ->title('لديك اشتراك نشط بالفعل')
                        ->warning()
                        ->send();
                    return;
                }
                
                // جلب المطبخ الافتراضي
                $kitchen = Kitchen::first();
                
                if (!$kitchen) {
                    Notification::make()
                        ->title('خطأ')
                        ->body('لا يوجد مطبخ متاح حالياً')
                        ->danger()
                        ->send();
                    return;
                }
                
                try {
                    // إنشاء اشتراك جديد بسعر 30 دينار شهرياً
                    $subscription = KitchenSubscription::create([
                        'subscription_number' => KitchenSubscription::generateSubscriptionNumber(),
                        'user_id' => $user->id,
                        'kitchen_id' => $kitchen->id,
                        'start_date' => now(),
                        'end_date' => now()->addYear(),
                        'status' => 'active',
                        'monthly_price' => 30.00, // السعر الشهري العادي
                        'notes' => 'اشتراك جديد',
                    ]);
                    
                    // إنشاء فاتورة الشهر الأول بسعر ترويجي 10 دنانير
                    $invoice = KitchenInvoice::create([
                        'invoice_number' => KitchenInvoice::generateInvoiceNumber(),
                        'subscription_id' => $subscription->id,
                        'user_id' => $user->id,
                        'amount' => 10.00, // السعر الترويجي للشهر الأول
                        'billing_date' => now(),
                        'due_date' => now()->endOfMonth(),
                        'status' => 'pending',
                    ]);
                    
                    Notification::make()
                        ->title('تم إنشاء الاشتراك بنجاح! 🎉')
                        ->body('رقم الاشتراك: ' . $subscription->subscription_number . ' | فاتورة الشهر الأول: ' . $invoice->amount . ' د.أ')
                        ->success()
                        ->send();
                    
                    // Refresh the widget
                    $this->dispatch('$refresh');
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('حدث خطأ')
                        ->body('لم نتمكن من إنشاء الاشتراك. يرجى المحاولة لاحقاً.')
                        ->danger()
                        ->send();
                }
            });
    }

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
