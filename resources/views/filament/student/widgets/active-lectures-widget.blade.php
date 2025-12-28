<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-xl font-bold tracking-tight">المحاضرات النشطة</span>
        </x-slot>

        @if($lectures->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-full">
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">لا توجد محاضرات نشطة حالياً</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">سيتم إدراج المحاضرات هنا عند بدئها</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($lectures as $lecture)
                    @php
                        $lectureStart = \Carbon\Carbon::parse($lecture->lecture_date);
                        $lectureEnd = $lectureStart->copy()->addMinutes($lecture->duration_minutes);
                        $hasAttended = $this->hasAttended($lecture->id);
                        $canRegister = $this->canRegisterAttendance($lecture);
                        $now = \Carbon\Carbon::now();
                        $timeRemaining = $now->diffInMinutes($lectureEnd, false);
                        $progress = 100 - (($timeRemaining / $lecture->duration_minutes) * 100);
                        $progress = max(0, min(100, $progress));
                    @endphp
                    
<<<<<<< HEAD
                    <div class="relative group p-6 rounded-2xl bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-primary-500 transition-all duration-300 flex flex-col gap-6">
                        
                        {{-- زر تسجيل الحضور في الأعلى --}}
                        <div>
                            @if($this->canRegisterAttendance($lecture))
                                <button 
                                    wire:click="registerAttendance({{ $lecture->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    wire:target="registerAttendance({{ $lecture->id }})"
                                    class="w-full relative py-4 px-6 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-black text-xl rounded-xl shadow-[0_6px_0_rgb(22,163,74)] hover:shadow-none translate-y-[-6px] hover:translate-y-0 transition-all flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="registerAttendance({{ $lecture->id }})">✓ تسجيل الحضور</span>
                                    <span wire:loading wire:target="registerAttendance({{ $lecture->id }})">جاري التسجيل...</span>
                                </button>
                            @else
                                <div class="w-full py-4 text-center bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold rounded-xl border border-dashed border-gray-200 dark:border-gray-600">
                                    غير متاح حالياً
=======
                    <div class="relative overflow-hidden rounded-2xl border transition-all duration-300
                        {{ $hasAttended 
                            ? 'bg-white dark:bg-gray-800 border-green-200 dark:border-green-900/50 shadow-sm' 
                            : 'bg-white dark:bg-gray-800 border-primary-200 dark:border-primary-900/50 shadow-md ring-2 ring-primary-50 dark:ring-primary-900/10' 
                        }}">
                        
                        {{-- Background Pattern --}}
                        <div class="absolute top-0 right-0 p-4 opacity-[0.03]">
                        </div>

                        <div class="p-5 flex flex-col h-full relative z-10">
                            {{-- Header --}}
                            <div class="mb-4 space-y-2">
                                {{-- Line 1: Course Title --}}
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">الدورة:</span>
                                    <span class="font-bold text-gray-900 dark:text-white mr-1">{{ $lecture->lesson?->title ?? 'دورة عامة' }}</span>
>>>>>>> fd81ecfbb86570820232ee7bd08200f0326cd7e3
                                </div>

                                {{-- Line 2: Lecture Number --}}
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">رقم المحاضرة:</span>
                                    <span class="font-bold text-gray-900 dark:text-white mr-1">{{ $lecture->id }}</span>
                                </div>

                                {{-- Line 3: Date and Time --}}
                                <div>
                                    @php
                                        $startAmPm = $lectureStart->format('A') == 'AM' ? 'صباحاً' : 'مساءً';
                                        $endAmPm = $lectureEnd->format('A') == 'AM' ? 'صباحاً' : 'مساءً';
                                    @endphp
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">التاريخ:</span>
                                    <span class="font-bold text-gray-900 dark:text-white mr-1">
                                        {{ $lectureStart->format('Y-m-d') }} 
                                        <span class="mx-1 text-gray-300">|</span> 
                                        {{ $lectureStart->format('g:i') }} {{ $startAmPm }} - {{ $lectureEnd->format('g:i') }} {{ $endAmPm }}
                                    </span>
                                </div>


                            </div>



                            {{-- Actions --}}
                            <div class="mt-auto pt-2">
                                @if($hasAttended)
                                    <div class="w-full py-3 px-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-center gap-2 cursor-default">
                                        <span class="font-bold text-green-700 dark:text-green-400">تم تسجيل الحضور</span>
                                    </div>
                                    <div class="text-center mt-2">
                                        <span class="text-xs text-green-600/70 dark:text-green-400/60">
                                            ✓ تم التأكيد في {{ \Carbon\Carbon::parse($lecture->attendances->first()?->marked_at)->format('h:i A') }}
                                        </span>
                                    </div>
                                @elseif($canRegister)
                                    <x-filament::button 
                                        wire:click="registerAttendance({{ $lecture->id }})"
                                        color="success"
                                        class="w-full">
                                        تسجيل الحضور الآن
                                    </x-filament::button>
                                @else
                                    <div class="w-full py-3.5 px-6 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 font-bold rounded-xl border border-dashed border-gray-200 dark:border-gray-700 text-center cursor-not-allowed">
                                        التسجيل غير متاح حالياً
                                    </div>
                                @endif
                            </div>
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
