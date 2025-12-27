<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-xl font-bold tracking-tight">المحاضرات النشطة</span>
        </x-slot>

        @if($lectures->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500 dark:text-gray-400">
                <p class="text-lg font-medium">لا توجد محاضرات نشطة حالياً</p>
                <p class="text-sm">سيتم إدراج المحاضرات هنا عند بدئها</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($lectures as $lecture)
                    @php
                        $lectureStart = \Carbon\Carbon::parse($lecture->lecture_date);
                        $lectureEnd = $lectureStart->copy()->addMinutes($lecture->duration_minutes);
                        $lectureNumber = $lecture->id;
                    @endphp
                    
                    <div class="relative group p-6 rounded-2xl bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-primary-500 transition-all duration-300 flex flex-col justify-between h-full">
                        
                        <div>
                            {{-- دورة: العنوان --}}
                            <div class="mb-4">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 block mb-1">دورة:</span>
                                <h3 class="text-xl font-black text-primary-600 dark:text-primary-400 leading-tight">
                                    {{ $lecture->lesson?->title ?? $lecture->title }}
                                </h3>
                            </div>

                            {{-- معلومات المحاضرة --}}
                            <div class="space-y-4 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                                        <p class="text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">محاضرة رقم</span>
                                            <span class="font-bold text-gray-900 dark:text-white mx-1">{{ $lectureNumber }}</span>
                                            <span class="text-gray-500 dark:text-gray-400 mx-1">في التاريخ</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $lectureStart->format('Y-m-d') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 pb-2 border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-bold">تبدأ في</span>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $lectureStart->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex flex-col text-left">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-bold">وتنتهي في</span>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $lectureEnd->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- زر تسجيل الحضور --}}
                        <div class="mt-auto">
                            @if($this->canRegisterAttendance($lecture))
                                <button 
                                    wire:click="registerAttendance({{ $lecture->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    wire:target="registerAttendance({{ $lecture->id }})"
                                    class="w-full relative py-4 px-6 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-extrabold text-lg rounded-xl shadow-[0_4px_0_rgb(22,163,74)] hover:shadow-none translate-y-[-4px] hover:translate-y-0 transition-all flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="registerAttendance({{ $lecture->id }})">✓ تسجيل الحضور</span>
                                    <span wire:loading wire:target="registerAttendance({{ $lecture->id }})">جاري التسجيل...</span>
                                </button>
                            @else
                                <div class="w-full py-4 text-center bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold rounded-xl border border-dashed border-gray-200 dark:border-gray-600">
                                    غير متاح حالياً
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
