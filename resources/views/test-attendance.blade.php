<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار نظام الحضور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8">اختبار نظام الحضور</h1>
            
            <!-- Create Test Data -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">إنشاء بيانات اختبار</h2>
                
                <form action="/create-test-data" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الدرس</label>
                        <input type="text" name="lesson_title" value="درس تجريبي - {{ date('Y-m-d H:i') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الدرس</label>
                            <input type="date" name="lesson_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">وقت البداية</label>
                            <input type="time" name="start_time" value="{{ date('H:i') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">فترة التحديث (بالثواني)</label>
                        <select name="refresh_interval" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="10">10 ثوانِ</option>
                            <option value="30" selected>30 ثانية</option>
                            <option value="60">60 ثانية</option>
                            <option value="120">120 ثانية</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition-colors">
                        إنشاء درس وكود حضور تجريبي
                    </button>
                </form>
            </div>
            
            <!-- Existing Codes -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">أكواد الحضور الموجودة</h2>
                
                @php
                    $attendanceCodes = \App\Models\AttendanceCode::with('lesson')
                        ->where('is_active', true)
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get();
                @endphp
                
                @if($attendanceCodes->count() > 0)
                    <div class="space-y-4">
                        @foreach($attendanceCodes as $code)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-semibold">{{ $code->lesson->title ?? 'درس غير محدد' }}</h3>
                                        <p class="text-sm text-gray-600">الكود: {{ $code->code }}</p>
                                        <p class="text-xs text-gray-500">
                                            تم الإنشاء: {{ $code->created_at->format('Y-m-d H:i:s') }}
                                        </p>
                                    </div>
                                    <div class="text-left">
                                        <a href="/attendance-code/{{ $code->id }}" 
                                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors inline-block">
                                            عرض الكود
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">لا توجد أكواد حضور نشطة حالياً</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>