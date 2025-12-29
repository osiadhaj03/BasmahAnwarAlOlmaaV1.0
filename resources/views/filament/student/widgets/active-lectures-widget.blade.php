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
                    
                    <div class="relative group p-6 rounded-2xl bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-primary-500 transition-all duration-300 flex flex-col gap-6">
                        
                        {{-- زر تسجيل الحضور في الأعلى --}}
                        <div>
                            <button 
                                wire:click="registerAttendance({{ $lecture->id }})"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                wire:target="registerAttendance({{ $lecture->id }})"
                                class="w-full relative py-4 px-6 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-black text-xl rounded-xl shadow-[0_6px_0_rgb(22,163,74)] hover:shadow-none translate-y-[-6px] hover:translate-y-0 transition-all flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="registerAttendance({{ $lecture->id }})">✓ تسجيل الحضور</span>
                                <span wire:loading wire:target="registerAttendance({{ $lecture->id }})">جاري التسجيل...</span>
                            </button>
                        </div>

                        {{-- معلومات المحاضرة --}}
                        <div class="space-y-4">
                            {{-- دورة: العنوان --}}
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-primary-500 dark:text-primary-400 block mb-1">دورة:</span>
                                <h3 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">
                                    {{ $lecture->lesson?->title ?? $lecture->title }}
                                </h3>
                            </div>

                            {{-- السطر الثاني: رقم المحاضرة والتاريخ --}}
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                                <p class="text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">محاضرة رقم</span>
                                    <span class="font-extrabold text-gray-900 dark:text-white mx-1 text-base">{{ $lectureNumber }}</span>
                                    <span class="text-gray-500 dark:text-gray-400 mx-1 text-sm">في التاريخ</span>
                                    <span class="font-bold text-gray-900 dark:text-white text-base">{{ $lectureStart->format('Y-m-d') }}</span>
                                </p>
                            </div>

                            {{-- السطر الثالث: أوقات البدء والانتهاء --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col p-2 bg-gray-50/50 dark:bg-gray-900/50 rounded-lg">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-black">تبدأ في</span>
                                    <span class="text-base font-extrabold text-gray-700 dark:text-gray-300">{{ $lectureStart->format('h:i A') }}</span>
                                </div>
                                <div class="flex flex-col p-2 bg-gray-50/50 dark:bg-gray-900/50 rounded-lg text-left">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-black">وتنتهي في</span>
                                    <span class="text-base font-extrabold text-gray-700 dark:text-gray-300">{{ $lectureEnd->format('h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
