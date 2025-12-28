<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-cake class="w-6 h-6 text-orange-500" />
                <span class="text-xl font-bold tracking-tight">اشتراك المطبخ</span>
            </div>
        </x-slot>

        @if(!$subscription)
            {{-- No Subscription State --}}
            <div class="flex flex-col items-center justify-center py-8 text-center bg-orange-50/50 dark:bg-orange-900/10 rounded-xl border border-orange-100 dark:border-orange-800/30">
                <div class="mb-3 p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                    <x-heroicon-o-shopping-bag class="w-8 h-8 text-orange-500" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا يوجد اشتراك نشط</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-4">اشترك الآن في خدمة المطبخ لتصلك وجبات يومية شهية</p>
                <x-filament::button color="warning" tag="a" href="#">
                    طلب اشتراك جديد
                </x-filament::button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Subscription Meta --}}
                <div class="col-span-1 md:col-span-2 space-y-4">
                    {{-- Status Banner --}}
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">حالة الاشتراك</p>
                            <div class="flex items-center gap-2">
                                @if($subscription->status == 'active')
                                    <span class="flex h-3 w-3 rounded-full bg-green-500 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    </span>
                                    <span class="font-bold text-lg text-green-600 dark:text-green-400">نشط</span>
                                @elseif($subscription->status == 'paused')
                                    <span class="h-3 w-3 rounded-full bg-yellow-500"></span>
                                    <span class="font-bold text-lg text-yellow-600 dark:text-yellow-400">موقوف مؤقتاً</span>
                                @else
                                    <span class="h-3 w-3 rounded-full bg-red-500"></span>
                                    <span class="font-bold text-lg text-red-600 dark:text-red-400">{{ $subscription->status_arabic }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-left rtl:text-right">
                            <p class="text-xs text-gray-400 mb-1">فترة الاشتراك</p>
                            <p class="font-medium text-gray-700 dark:text-gray-200">
                                {{ $subscription->start_date->format('Y-m-d') }} <span class="text-gray-400 mx-1">إلى</span> {{ $subscription->end_date->format('Y-m-d') }}
                            </p>
                        </div>
                    </div>

                    {{-- Today's Meal Status --}}
                    <div class="p-4 rounded-xl border border-dashed {{ $todayMeal ? 'bg-green-50/50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ $todayMeal ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500' }}">
                                <x-heroicon-m-truck class="w-6 h-6" />
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">وجبة اليوم</h4>
                                @if($todayMeal)
                                    @if($todayMeal->status == 'delivered')
                                        <p class="text-sm text-green-600 font-medium">تم التوصيل بنجاح ✓</p>
                                    @elseif($todayMeal->status == 'pending')
                                        <p class="text-sm text-orange-600 font-medium">قيد التوصيل...</p>
                                    @else
                                        <p class="text-sm text-red-600 font-medium">{{ $todayMeal->status_arabic }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-500">لا توجد وجبة مجدولة لليوم</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Column --}}
                <div class="col-span-1 space-y-3">
                    <div class="p-4 rounded-xl bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800/30">
                        <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider mb-1">رصيد الحساب</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white dir-ltr text-right">
                            {{ number_format($stats['balance'], 2) }} <span class="text-sm font-medium text-gray-500">د.أ</span>
                        </p>
                        @if($stats['balance'] < 0)
                            <p class="text-xs text-red-500 mt-1 font-bold">يرجى سداد المبلغ المستحق</p>
                        @else
                            <p class="text-xs text-green-500 mt-1 font-bold">رصيد متوفر</p>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30">
                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">وجبات الشهر</p>
                        <div class="flex items-baseline gap-1">
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['monthly_meals'] }}</p>
                            <span class="text-sm text-gray-500">وجبة مستلمة</span>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($stats['last_invoice'] && $stats['last_invoice']->status == 'pending')
                 <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-red-700">
                        <x-heroicon-s-exclamation-circle class="w-5 h-5" />
                        <span class="text-sm font-bold">فاتورة غير مدفوعة: {{ $stats['last_invoice']->invoice_number }}</span>
                    </div>
                    <span class="font-bold text-red-600">{{ $stats['last_invoice']->amount }} د.أ</span>
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
