<div>
    {{-- زر فتح الـ Modal --}}
    <x-filament::button
        wire:click="openModal"
        color="primary"
        icon="heroicon-o-calculator"
    >
        📊 حساب الفواتير التلقائي
    </x-filament::button>

    {{-- الـ Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeModal">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden" dir="rtl">
            {{-- Header --}}
            <div class="bg-gradient-to-l from-primary-500 to-primary-600 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <x-heroicon-o-calculator class="w-6 h-6"/>
                    حساب الفواتير التلقائي
                </h2>
                <button wire:click="closeModal" class="text-white/80 hover:text-white transition">
                    <x-heroicon-o-x-mark class="w-6 h-6"/>
                </button>
            </div>

            {{-- Content --}}
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                {{-- اختيار الفترة --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3 flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="w-5 h-5"/>
                        تحديد الفترة
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">من تاريخ</label>
                            <input 
                                type="date" 
                                wire:model="fromDate"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">إلى تاريخ</label>
                            <input 
                                type="date" 
                                wire:model="toDate"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                            >
                        </div>
                        <div class="flex items-end">
                            <x-filament::button
                                wire:click="calculate"
                                wire:loading.attr="disabled"
                                color="success"
                                icon="heroicon-o-magnifying-glass"
                                class="w-full"
                            >
                                <span wire:loading.remove wire:target="calculate">🔍 حساب</span>
                                <span wire:loading wire:target="calculate">جاري الحساب...</span>
                            </x-filament::button>
                        </div>
                    </div>
                </div>

                {{-- جدول المشتركين --}}
                @if($calculated)
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            المحددين: <strong>{{ count($selectedSubscribers) }}</strong> من <strong>{{ count($subscribers) }}</strong>
                        </span>
                        <x-filament::button size="sm" color="gray" wire:click="selectAll">
                            تحديد الكل
                        </x-filament::button>
                        <x-filament::button size="sm" color="gray" wire:click="deselectAll">
                            إلغاء التحديد
                        </x-filament::button>
                    </div>
                    <div class="bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-4 py-2 rounded-lg font-bold">
                        الإجمالي: {{ number_format($totalAmount, 2) }} ₪
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">✓</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">الاسم</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">النوع</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">المحاضرات</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">الحضور</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">الغيابات</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">سعر الغياب</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($subscribers as $subscriber)
                            <tr 
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition {{ $subscriber['is_full_price'] ? 'bg-red-50 dark:bg-red-900/10' : '' }}"
                                wire:click="toggleSubscriber({{ $subscriber['user_id'] }})"
                            >
                                <td class="px-4 py-3">
                                    <input 
                                        type="checkbox" 
                                        {{ in_array($subscriber['user_id'], $selectedSubscribers) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        wire:click.stop="toggleSubscriber({{ $subscriber['user_id'] }})"
                                    >
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $subscriber['name'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ str_contains($subscriber['type'], 'طالب') ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' }}
                                    ">
                                        {{ $subscriber['type'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $subscriber['lectures_count'] }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($subscriber['attendance_count'] !== '-')
                                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $subscriber['attendance_count'] }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($subscriber['absence_count'] !== '-')
                                        <span class="text-red-600 dark:text-red-400 font-medium">{{ $subscriber['absence_count'] }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    @if($subscriber['absence_price'] !== '-')
                                        {{ number_format($subscriber['absence_price'], 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold {{ $subscriber['is_full_price'] ? 'text-red-600 dark:text-red-400' : 'text-primary-600 dark:text-primary-400' }}">
                                        {{ number_format($subscriber['amount'], 2) }} ₪
                                        @if($subscriber['is_full_price'])
                                            <span class="text-xs text-red-500">(كامل)</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    لا يوجد مشتركين فعالين
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @elseif(!$calculated)
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-document-text class="w-16 h-16 mx-auto mb-4 opacity-50"/>
                    <p>اختر الفترة ثم اضغط "حساب" لعرض المشتركين</p>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            @if($calculated && count($selectedSubscribers) > 0)
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
                <div class="text-gray-600 dark:text-gray-300">
                    سيتم إنشاء <strong class="text-primary-600">{{ count($selectedSubscribers) }}</strong> فاتورة
                </div>
                <div class="flex items-center gap-3">
                    <x-filament::button color="gray" wire:click="closeModal">
                        إلغاء
                    </x-filament::button>
                    <x-filament::button 
                        color="success" 
                        wire:click="generateInvoices"
                        wire:loading.attr="disabled"
                        icon="heroicon-o-document-plus"
                    >
                        <span wire:loading.remove wire:target="generateInvoices">✅ إنشاء الفواتير</span>
                        <span wire:loading wire:target="generateInvoices">جاري الإنشاء...</span>
                    </x-filament::button>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
