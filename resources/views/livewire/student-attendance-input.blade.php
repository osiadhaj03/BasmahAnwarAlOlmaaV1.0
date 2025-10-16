<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">تسجيل الحضور</h1>
            <p class="text-gray-600">أدخل كود الحضور الخاص بالدرس لتسجيل حضورك</p>
        </div>
    </div>

    <!-- Message Display -->
    @if($message)
        <div class="mb-6">
            <div class="p-4 rounded-lg border-l-4 {{ 
                $messageType === 'success' ? 'bg-green-50 border-green-400 text-green-700' : 
                ($messageType === 'error' ? 'bg-red-50 border-red-400 text-red-700' : 
                ($messageType === 'warning' ? 'bg-yellow-50 border-yellow-400 text-yellow-700' : 
                'bg-blue-50 border-blue-400 text-blue-700'))
            }}">
                <div class="flex justify-between items-center">
                    <p class="font-medium">{{ $message }}</p>
                    <button wire:click="clearMessage" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Form -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        <form wire:submit.prevent="submitAttendance" class="space-y-6">
            <!-- Student Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="studentName" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم الطالب <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="studentName"
                           wire:model="studentName" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="أدخل اسمك الكامل">
                    @error('studentName') 
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label for="studentId" class="block text-sm font-medium text-gray-700 mb-2">
                        الرقم الجامعي <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="studentId"
                           wire:model="studentId" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="أدخل رقمك الجامعي">
                    @error('studentId') 
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            <!-- Attendance Code -->
            <div>
                <label for="attendanceCode" class="block text-sm font-medium text-gray-700 mb-2">
                    كود الحضور <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-3">
                    <input type="text" 
                           id="attendanceCode"
                           wire:model.live="attendanceCode" 
                           wire:keyup="checkCodeStatus"
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-lg font-mono"
                           placeholder="أدخل كود الحضور"
                           maxlength="10">
                    <button type="button" 
                            wire:click="checkCodeStatus"
                            class="px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        تحقق
                    </button>
                </div>
                @error('attendanceCode') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Current Lesson Info -->
            @if($currentLesson)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-800 mb-2">معلومات الدرس</h3>
                    <div class="text-sm text-blue-700">
                        <p><strong>العنوان:</strong> {{ $currentLesson->title }}</p>
                        <p><strong>التاريخ:</strong> {{ $currentLesson->date ? $currentLesson->date->format('Y-m-d') : 'غير محدد' }}</p>
                        <p><strong>الوقت:</strong> {{ $currentLesson->start_time }} - {{ $currentLesson->end_time }}</p>
                        @if($currentLesson->description)
                            <p><strong>الوصف:</strong> {{ $currentLesson->description }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="submitAttendance"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                    <span wire:loading.remove wire:target="submitAttendance">تسجيل الحضور</span>
                    <span wire:loading wire:target="submitAttendance" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري التسجيل...
                    </span>
                </button>
                
                <button type="button" 
                        wire:click="resetForm"
                        class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    إعادة تعيين
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Attendances -->
    @if(count($recentAttendances) > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">آخر حضوراتك</h3>
            
            <div class="space-y-3">
                @foreach($recentAttendances as $attendance)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $attendance->lesson->title ?? 'درس غير محدد' }}</h4>
                                <p class="text-sm text-gray-600">
                                    تاريخ الحضور: {{ $attendance->attended_at ? $attendance->attended_at->format('Y-m-d H:i') : 'غير محدد' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    الكود المستخدم: {{ $attendance->attendanceCode->code ?? 'غير محدد' }}
                                </p>
                            </div>
                            <div class="text-left">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ 
                                    $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                    ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 
                                    'bg-yellow-100 text-yellow-800')
                                }}">
                                    {{ $attendance->status === 'present' ? 'حاضر' : 
                                       ($attendance->status === 'absent' ? 'غائب' : 'متأخر') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Instructions -->
    <div class="bg-gray-50 rounded-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">تعليمات الاستخدام</h3>
        <ul class="text-sm text-gray-600 space-y-2">
            <li class="flex items-start">
                <span class="text-blue-500 mr-2">•</span>
                أدخل اسمك الكامل والرقم الجامعي بشكل صحيح
            </li>
            <li class="flex items-start">
                <span class="text-blue-500 mr-2">•</span>
                احصل على كود الحضور من المعلم أو من الشاشة المعروضة في القاعة
            </li>
            <li class="flex items-start">
                <span class="text-blue-500 mr-2">•</span>
                أدخل الكود بدقة - الأكواد حساسة للأحرف الكبيرة والصغيرة
            </li>
            <li class="flex items-start">
                <span class="text-blue-500 mr-2">•</span>
                يمكنك تسجيل الحضور مرة واحدة فقط لكل درس
            </li>
            <li class="flex items-start">
                <span class="text-blue-500 mr-2">•</span>
                تأكد من تسجيل حضورك خلال الوقت المحدد للدرس
            </li>
        </ul>
    </div>
</div>
