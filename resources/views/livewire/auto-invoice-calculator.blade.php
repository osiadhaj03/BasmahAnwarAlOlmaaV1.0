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
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
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
                                wire:model.live="fromDate"
                                wire:change="updateDates"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">إلى تاريخ</label>
                            <input 
                                type="date" 
                                wire:model.live="toDate"
                                wire:change="updateDates"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                            >
                        </div>
                        <div class="flex items-end">
                            <div class="bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-4 py-2 rounded-lg text-sm w-full text-center">
                                <span class="font-bold">الفترة:</span> {{ $fromDate }} → {{ $toDate }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filament Table --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            💡 <strong>تلميح:</strong> حدد المشتركين من القائمة ثم اضغط على زر "إنشاء فواتير للمحددين" 
                        </p>
                    </div>
                    {{ $this->table }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
