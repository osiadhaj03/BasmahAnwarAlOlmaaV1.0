<x-filament-panels::page>
    {{-- فورم اختيار الفترة --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar-days class="w-5 h-5"/>
                تحديد الفترة
            </div>
        </x-slot>
        <x-slot name="description">
            اختر الفترة لحساب الفواتير بناءً على حضور وغياب الطلاب
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                <input 
                    type="date" 
                    wire:model.live="fromDate"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary-500 focus:border-primary-500"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                <input 
                    type="date" 
                    wire:model.live="toDate"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary-500 focus:border-primary-500"
                >
            </div>
            <div class="flex items-end">
                <div class="bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-4 py-2.5 rounded-lg font-medium w-full text-center">
                    📅 الفترة: {{ $fromDate }} → {{ $toDate }}
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- تعليمات --}}
    <x-filament::section>
        <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-warning-500"/>
            <span>
                <strong>تلميح:</strong> 
                حدد المشتركين من الجدول ثم اضغط على زر <strong>"إنشاء فواتير للمحددين"</strong> من شريط الإجراءات.
                الصفوف الحمراء تعني أن المشترك سيدفع الاشتراك الكامل (زبون أو طالب بدون حضور).
            </span>
        </div>
    </x-filament::section>

    {{-- الجدول --}}
    {{ $this->table }}
</x-filament-panels::page>
