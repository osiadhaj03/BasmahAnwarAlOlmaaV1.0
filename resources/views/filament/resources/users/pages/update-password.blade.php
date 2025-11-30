@php
    use Filament\Support\Facades\FilamentView;
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                تحديث كلمة المرور
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                إدخال كلمة مرور جديدة آمنة للمستخدم: <strong>{{ $record->name }}</strong>
            </p>

            <form wire:submit="submit" class="space-y-6">
                {{ $this->form }}

                <div class="flex gap-4">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-m-check"
                        color="success"
                    >
                        تحديث كلمة المرور
                    </x-filament::button>

                    <a href="{{ route('filament.admin.resources.users.view', ['record' => $record->id]) }}"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                💡 نصائح لكلمة مرور آمنة:
            </h3>
            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                <li>✓ استخدم 8 أحرف على الأقل</li>
                <li>✓ أضف أحرفاً كبيرة وصغيرة</li>
                <li>✓ أضف أرقاماً</li>
                <li>✓ تجنب استخدام معلومات شخصية</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
