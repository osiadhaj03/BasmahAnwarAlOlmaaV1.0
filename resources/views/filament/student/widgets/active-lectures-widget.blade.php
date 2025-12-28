<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-xl font-bold tracking-tight">المحاضرات النشطة</span>
        </x-slot>

        @if($lectures->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-full">
                    <x-heroicon-o-calendar class="w-12 h-12 text-gray-400" />
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
                            <x-heroicon-o-academic-cap class="w-32 h-32" />
                        </div>

                        <div class="p-5 flex flex-col h-full relative z-10">
                            {{-- Header --}}
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $hasAttended ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' }}">
                                        {{ $lecture->lesson?->title ?? 'دورة عامة' }}
                                    </span>
                                    <h3 class="mt-2 text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $lecture->title }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $lectureStart->format('h:i A') }} - {{ $lectureEnd->format('h:i A') }}
                                    </p>
                                </div>
                                
                                @if(!$hasAttended)
                                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <span class="text-xs font-bold uppercase tracking-wider">مباشر</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Progress Bar (Only for active not attended) --}}
                            @if(!$hasAttended)
                                <div class="mb-6">
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-gray-500">الوقت المنقضي</span>
                                        <span class="text-primary-600 font-medium">{{ (int)$progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                        <div class="bg-primary-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                                    </div>
                                    @if($timeRemaining > 0)
                                        <p class="text-xs text-gray-400 mt-1.5 text-left rtl:text-right">
                                            متبقي {{ (int)$timeRemaining }} دقيقة
                                        </p>
                                    @endif
                                </div>
                            @else
                                <div class="mb-4 py-3 border-t border-b border-gray-100 dark:border-gray-700/50">
                                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-m-calendar class="w-4 h-4 text-gray-400" />
                                            <span>{{ $lectureStart->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-m-clock class="w-4 h-4 text-gray-400" />
                                            <span>{{ $lecture->duration_minutes }} دقيقة</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-auto pt-2">
                                @if($hasAttended)
                                    <div class="w-full py-3 px-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-center gap-2 cursor-default">
                                        <div class="bg-green-500 text-white p-0.5 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-green-700 dark:text-green-400">تم تسجيل الحضور</span>
                                    </div>
                                    <div class="text-center mt-2">
                                        <span class="text-xs text-green-600/70 dark:text-green-400/60">
                                            ✓ تم التأكيد في {{ \Carbon\Carbon::parse($lecture->attendances->first()?->marked_at)->format('h:i A') }}
                                        </span>
                                    </div>
                                @elseif($canRegister)
                                    <button 
                                        wire:click="registerAttendance({{ $lecture->id }})"
                                        wire:loading.attr="disabled"
                                        class="w-full relative group overflow-hidden py-3.5 px-6 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 dark:shadow-none transition-all duration-300 transform active:scale-[0.98]">
                                        <div class="relative z-10 flex items-center justify-center gap-2">
                                            <span wire:loading.remove wire:target="registerAttendance({{ $lecture->id }})">تسجيل الحضور الآن</span>
                                            <span wire:loading wire:target="registerAttendance({{ $lecture->id }})">جاري التسجيل...</span>
                                            <x-heroicon-m-check-circle class="w-5 h-5" wire:loading.remove wire:target="registerAttendance({{ $lecture->id }})" />
                                        </div>
                                    </button>
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
