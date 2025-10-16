<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">عرض كود الحضور</h1>
                <p class="text-gray-600 mt-2">{{ $lesson->title ?? 'غير محدد' }}</p>
                <p class="text-sm text-gray-500">
                    تاريخ الدرس: {{ $lesson->date ? $lesson->date->format('Y-m-d') : 'غير محدد' }} | 
                    الوقت: {{ $lesson->start_time ?? 'غير محدد' }} - {{ $lesson->end_time ?? 'غير محدد' }}
                </p>
            </div>
            <div class="text-left">
                <button wire:click="stopDisplay" 
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition-colors">
                    إيقاف العرض
                </button>
            </div>
        </div>
    </div>

    <!-- Main Code Display -->
    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg shadow-2xl p-12 mb-6 text-center">
        <div class="bg-white rounded-lg p-8 mx-auto max-w-md">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">كود الحضور</h2>
            
            @if($attendanceCode && $attendanceCode->isCurrentlyActive())
                <div class="code-display text-blue-600 mb-4 {{ $isAutoRefreshEnabled ? 'refresh-animation' : '' }}">
                    {{ $attendanceCode->code }}
                </div>
                
                @if($isAutoRefreshEnabled)
                    <div class="text-gray-600 mb-4">
                        <p class="text-sm">التحديث التالي خلال:</p>
                        <span id="countdown" class="countdown text-orange-500">{{ $secondsUntilRefresh }}</span>
                        <span class="text-sm">ثانية</span>
                    </div>
                @endif
                
                <div class="text-xs text-gray-500">
                    آخر تحديث: {{ $attendanceCode->last_refreshed_at ? $attendanceCode->last_refreshed_at->format('H:i:s') : 'لم يتم التحديث' }}
                </div>
            @else
                <div class="text-red-500 text-xl font-bold">
                    الكود غير نشط حالياً
                </div>
            @endif
        </div>
    </div>

    <!-- Controls -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">التحكم في الكود</h3>
        
        <div class="flex flex-wrap gap-4">
            <button wire:click="toggleAutoRefresh" 
                    class="px-4 py-2 rounded-lg transition-colors {{ $isAutoRefreshEnabled ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-700' }}">
                {{ $isAutoRefreshEnabled ? 'إيقاف التحديث التلقائي' : 'تفعيل التحديث التلقائي' }}
            </button>
            
            <button wire:click="manualRefresh" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                تحديث يدوي
            </button>
            
            <button wire:click="$refresh" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                تحديث البيانات
            </button>
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>فترة التحديث:</strong> {{ $attendanceCode->refresh_interval ?? 30 }} ثانية</p>
            <p><strong>وقت بدء العرض:</strong> {{ $attendanceCode->display_started_at ? $attendanceCode->display_started_at->format('H:i:s') : 'غير محدد' }}</p>
            <p><strong>انتهاء صلاحية الكود:</strong> {{ $attendanceCode->expires_at ? $attendanceCode->expires_at->format('Y-m-d H:i:s') : 'غير محدد' }}</p>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Students -->
        <div class="stats-card">
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">{{ $attendanceStats['total'] ?? 0 }}</div>
                <div class="text-sm opacity-90">إجمالي الطلاب</div>
            </div>
        </div>
        
        <!-- Present Students -->
        <div class="bg-green-500 text-white rounded-lg p-6 text-center shadow-lg">
            <div class="text-3xl font-bold mb-2">{{ $attendanceStats['present'] ?? 0 }}</div>
            <div class="text-sm opacity-90">الحاضرون</div>
        </div>
        
        <!-- Absent Students -->
        <div class="bg-red-500 text-white rounded-lg p-6 text-center shadow-lg">
            <div class="text-3xl font-bold mb-2">{{ $attendanceStats['absent'] ?? 0 }}</div>
            <div class="text-sm opacity-90">الغائبون</div>
        </div>
        
        <!-- Attendance Percentage -->
        <div class="bg-yellow-500 text-white rounded-lg p-6 text-center shadow-lg">
            <div class="text-3xl font-bold mb-2">{{ $attendanceStats['percentage'] ?? 0 }}%</div>
            <div class="text-sm opacity-90">نسبة الحضور</div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">معلومات إضافية</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">تفاصيل الكود</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><strong>معرف الكود:</strong> {{ $attendanceCode->id ?? 'غير محدد' }}</li>
                    <li><strong>عدد مرات الاستخدام:</strong> {{ $attendanceCode->usage_count ?? 0 }}</li>
                    <li><strong>الحد الأقصى للاستخدام:</strong> {{ $attendanceCode->max_usage ?? 'غير محدود' }}</li>
                    <li><strong>منشئ الكود:</strong> {{ $attendanceCode->createdBy->name ?? 'غير محدد' }}</li>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">حالة النظام</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><strong>حالة الكود:</strong> 
                        <span class="px-2 py-1 rounded text-xs {{ $attendanceCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $attendanceCode->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </li>
                    <li><strong>التحديث التلقائي:</strong> 
                        <span class="px-2 py-1 rounded text-xs {{ $isAutoRefreshEnabled ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $isAutoRefreshEnabled ? 'مفعل' : 'معطل' }}
                        </span>
                    </li>
                    <li><strong>وقت التحديث الحالي:</strong> {{ now()->format('H:i:s') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
</div>
