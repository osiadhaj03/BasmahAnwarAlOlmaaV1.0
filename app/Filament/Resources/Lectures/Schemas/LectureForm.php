<?php

namespace App\Filament\Resources\Lectures\Schemas;

use App\Models\Lesson;
use App\Models\Lecture;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class LectureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات المحاضرة الأساسية')
                    ->description('المعلومات الأساسية للمحاضرة')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان المحاضرة')
                            ->required()
                            ->maxLength(255),
                        Select::make('lesson_id')
                            ->label('الدورة')
                            ->options(Lesson::all()->pluck('title', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                if (!$state) {
                                    return;
                                }
                                
                                $lesson = Lesson::find($state);
                                if (!$lesson) {
                                    return;
                                }
                                
                                // حساب تاريخ ووقت المحاضرة التالية
                                $nextLectureDateTime = self::calculateNextLectureDateTime($lesson);
                                
                                if ($nextLectureDateTime) {
                                    $set('lecture_date', $nextLectureDateTime->format('Y-m-d H:i:s'));
                                }
                                
                                // تحديث رقم المحاضرة التالي
                                $nextLectureNumber = $lesson->lectures()->max('lecture_number') + 1 ?? 1;
                                $set('lecture_number', $nextLectureNumber);
                            }),

                        TextInput::make('lecture_number')
                            ->label('رقم المحاضرة')
                            ->numeric()
                            ->default(function () {
                                return Lecture::max('lecture_number') + 1 ?? 1;
                            })
                            ->required()
                            ->minValue(1),

                        Textarea::make('description')
                            ->label('وصف المحاضرة')
                            ->rows(3)
                            ->columnSpanFull()
                            ->columnSpanFull(),

                        
                    ])->columnSpan('full')->columns(3),

                Section::make('التوقيت والمكان')
                    ->description('معلومات التوقيت والمكان')
                    ->schema([
                        DateTimePicker::make('lecture_date')
                            ->label('تاريخ ووقت المحاضرة')
                            ->seconds(false),

                        TextInput::make('duration_minutes')
                            ->label('مدة المحاضرة (بالدقائق)')
                            ->numeric()
                            ->default(60)
                            ->required()
                            ->minValue(1),

                        Select::make('location')
                            ->label('مكان المحاضرة')
                            ->options([
                                'وجاهي' => 'وجاهي',
                                'اونلاين' => 'اونلاين',
                            ])
                            ->default('وجاهي')
                            ->required(),


                    ])->columnSpan('full')
                    ->columns(3),

                Section::make('إعدادات إضافية')
                    ->description('الإعدادات والملاحظات')
                    ->schema([
                        //Toggle::make('is_mandatory')
                        //    ->label('محاضرة إجبارية')
                        //    ->default(true),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),

                        //TextInput::make('recording_url')
                        //    ->label('رابط تسجيل المحاضرة')
                        //    ->url()
                        //    ->maxLength(255)
                        //    ->columnSpanFull(),
                    ])->columnSpan('full')
                    ->columns(2),
            ]);
    }

    /**
     * حساب تاريخ ووقت المحاضرة التالية بناءً على جدول الدرس
     */
    private static function calculateNextLectureDateTime(Lesson $lesson): ?Carbon
    {
        // إذا لم تكن هناك أيام محددة للدرس، استخدم التاريخ الحالي
        if (!$lesson->lesson_days || !is_array($lesson->lesson_days) || empty($lesson->lesson_days)) {
            $nextDate = Carbon::now();
            
            // إذا كان هناك وقت بداية محدد، استخدمه
            if ($lesson->start_time) {
                $startTime = Carbon::parse($lesson->start_time);
                $nextDate->setTime($startTime->hour, $startTime->minute);
            } else {
                // وقت افتراضي 9:00 صباحاً
                $nextDate->setTime(9, 0);
            }
            
            return $nextDate;
        }

        // تحويل أيام الأسبوع إلى أرقام (0 = الأحد، 6 = السبت)
        $dayMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];

        $lessonDayNumbers = [];
        foreach ($lesson->lesson_days as $day) {
            if (isset($dayMap[strtolower($day)])) {
                $lessonDayNumbers[] = $dayMap[strtolower($day)];
            }
        }

        if (empty($lessonDayNumbers)) {
            return null;
        }

        // البحث عن أقرب يوم من أيام الدرس
        $today = Carbon::now();
        $currentDayOfWeek = $today->dayOfWeek;
        
        // البحث عن اليوم التالي من أيام الدرس
        $nextDay = null;
        $daysToAdd = 0;

        // البحث في الأيام المتبقية من الأسبوع الحالي
        for ($i = 0; $i < 7; $i++) {
            $checkDay = ($currentDayOfWeek + $i) % 7;
            if (in_array($checkDay, $lessonDayNumbers)) {
                // إذا كان اليوم الحالي، تحقق من الوقت
                if ($i == 0 && $lesson->start_time) {
                    $startTime = Carbon::parse($lesson->start_time);
                    $todayWithLessonTime = $today->copy()->setTime($startTime->hour, $startTime->minute);
                    
                    // إذا لم يفت الوقت بعد، استخدم اليوم الحالي
                    if ($todayWithLessonTime->isFuture()) {
                        $daysToAdd = 0;
                        break;
                    }
                } elseif ($i > 0) {
                    $daysToAdd = $i;
                    break;
                }
            }
        }

        // حساب التاريخ التالي
        $nextDate = $today->copy()->addDays($daysToAdd);
        
        // تطبيق وقت بداية الدرس
        if ($lesson->start_time) {
            $startTime = Carbon::parse($lesson->start_time);
            $nextDate->setTime($startTime->hour, $startTime->minute);
        } else {
            // وقت افتراضي 9:00 صباحاً
            $nextDate->setTime(9, 0);
        }

        // التأكد من أن التاريخ ضمن فترة الدرس
        if ($lesson->start_date && $nextDate->lt(Carbon::parse($lesson->start_date))) {
            $nextDate = Carbon::parse($lesson->start_date);
            if ($lesson->start_time) {
                $startTime = Carbon::parse($lesson->start_time);
                $nextDate->setTime($startTime->hour, $startTime->minute);
            }
        }

        if ($lesson->end_date && $nextDate->gt(Carbon::parse($lesson->end_date))) {
            return null; // الدرس انتهى
        }

        return $nextDate;
    }
}
