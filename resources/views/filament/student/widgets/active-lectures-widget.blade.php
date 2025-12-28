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
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
