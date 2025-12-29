<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-xl font-bold tracking-tight">اشتراك المطبخ</span>
        </x-slot>

        @if(!$subscription)
            {{-- No Subscription State --}}
            <div class="flex flex-col items-center justify-center py-8 text-center bg-orange-50/50 dark:bg-orange-900/10 rounded-xl border border-orange-100 dark:border-orange-800/30">
                <div class="mb-3 p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا يوجد اشتراك نشط</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-4">اشترك الآن في خدمة المطبخ لتصلك وجبات يومية شهية</p>
                <x-filament::button color="warning" tag="a" href="#">
                    طلب اشتراك جديد
                </x-filament::button>
            </div>
        @else
            <div class="overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Status --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 w-1/3 bg-gray-50 dark:bg-gray-800/50">
حالة الاشتراك:
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    @if($subscription->status == 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            نشط
                                        </span>
                                    @elseif($subscription->status == 'paused')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            موقوف مؤقتاً
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ $subscription->status_arabic }}
                                        </span>
                                    @endif
                                </div>
                        </div>
                    </div>

                       

                        {{-- Today's Meal --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50">
                                وجبة اليوم:
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                @if($todayMeal)
                                    <div class="flex items-center gap-2">
                                        @if($todayMeal->status == 'delivered')
                                            <span class="text-green-700 dark:text-green-400 font-medium">تم الإستلام</span>
                                        @elseif($todayMeal->status == 'pending')
                                            <span class="text-orange-700 dark:text-orange-400 font-medium">قيد الإستلام</span>
                                        @else
                                            <span class="text-red-700 dark:text-red-400 font-medium">{{ $todayMeal->status_arabic }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">لا توجد وجبة اليوم</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @if($stats['last_invoice'] && $stats['last_invoice']->status == 'pending')
                 <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-red-700">
                        <span class="text-sm font-bold">فاتورة غير مدفوعة: {{ $stats['last_invoice']->invoice_number }} وقيمتها: {{ $stats['last_invoice']->amount }} د.أ</span>
                    </div>
                    <span class="font-bold text-red-600">يرجى دفع الفاتورة</span>
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
