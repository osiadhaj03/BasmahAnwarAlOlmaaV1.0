<x-filament-panels::page>
    <div class="space-y-6">
        <!-- فلاتر البحث -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">فلاتر البحث</h3>
            {{ $this->form }}
        </div>

        <!-- الإحصائيات العامة -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($this->getStats() as $stat)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($stat->getColor() === 'success')
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @elseif($stat->getColor() === 'danger')
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @elseif($stat->getColor() === 'warning')
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="mr-4">
                            <p class="text-sm font-medium text-gray-600">{{ $stat->getLabel() }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stat->getValue() }}</p>
                            @if($stat->getDescription())
                                <p class="text-sm text-gray-500">{{ $stat->getDescription() }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- جدول البيانات التفصيلية -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">سجلات الحضور التفصيلية</h3>
            </div>
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-blue-800">معلومات مهمة</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>يمكنك استخدام الفلاتر أعلاه لتخصيص النتائج حسب الدورة أو المعلم أو الفترة الزمنية</li>
                            <li>الإحصائيات تحدث تلقائياً عند تغيير الفلاتر</li>
                            <li>يمكن تصدير البيانات من خلال خيارات الجدول</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>