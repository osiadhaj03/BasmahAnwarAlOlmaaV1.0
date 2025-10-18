<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Lesson;
use App\Models\Lecture;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الحضور الأساسية')
                    ->description('المعلومات الأساسية لتسجيل الحضور')
                    ->schema([
                        Select::make('lesson_id')
                            ->label('الدورة')
                            ->options(Lesson::with('teacher')->get()->pluck('title_with_teacher', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                // إعادة تعيين المحاضرة عند تغيير الدورة
                                $set('lecture_id', null);
                                $set('attendance_date', null);
                            }),
                        
                        Select::make('lecture_id')
                            ->label('المحاضرة')
                            ->options(function ($get) {
                                $lessonId = $get('lesson_id');
                                if (!$lessonId) {
                                    return [];
                                }
                                return Lecture::where('lesson_id', $lessonId)
                                    ->orderBy('lecture_number')
                                    ->get()
                                    ->mapWithKeys(function ($lecture) {
                                        return [$lecture->id => "محاضرة {$lecture->lecture_number}: {$lecture->title}"];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $lecture = Lecture::find($state);
                                    if ($lecture && $lecture->lecture_date) {
                                        $set('attendance_date', $lecture->lecture_date);
                                    }
                                }
                            })
                            ->disabled(fn ($get) => !$get('lesson_id'))
                            ->helperText('اختر الدورة أولاً لعرض المحاضرات المتاحة'),
                        
                        Select::make('student_id')
                            ->label('الطالب')
                            ->options(User::where('type', 'student')->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Select::make('status')
                            ->label('حالة الحضور')
                            ->options([
                                'present' => 'حاضر',
                                'absent' => 'غائب',
                                'late' => 'متأخر',
                                'excused' => 'معذور',
                            ])
                            ->default('present')
                            ->required(),
                        
                        DateTimePicker::make('attendance_date')
                            ->label('تاريخ الحضور')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->default(function ($get) {
                                $lectureId = $get('lecture_id');
                                if ($lectureId) {
                                    $lecture = Lecture::find($lectureId);
                                    if ($lecture && $lecture->lecture_date) {
                                        return $lecture->lecture_date;
                                    }
                                }
                                return now();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $lectureId = $get('lecture_id');
                                if ($lectureId && !$state) {
                                    $lecture = Lecture::find($lectureId);
                                    if ($lecture && $lecture->lecture_date) {
                                        $set('attendance_date', $lecture->lecture_date);
                                    }
                                }
                            })
                            ->helperText('سيتم تعيين تاريخ ووقت المحاضرة المختارة تلقائياً'),
                    ])->columnSpan('full') ->columns(4),
                
                Section::make('تفاصيل طريقة التسجيل')
                    ->description('معلومات حول كيفية تسجيل الحضور')
                    ->schema([

                        TextInput::make('attendance_method')
                            ->label('طريقة التسجيل')
                            ->default('manual')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('التسجيل اليدوي فقط في هذه الصفحة'),
                        
                        DateTimePicker::make('marked_at')
                            ->label('وقت تسجيل الحضور')
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->default(now())
                            ->disabled()
                            ->dehydrated()
                            ->helperText('يتم تسجيل الوقت الحالي تلقائياً عند الحفظ'),
                        
                        TextInput::make('marked_by_name')
                            ->label('سجل بواسطة')
                            ->default(function () {
                                $user = auth()->user();
                                return $user ? $user->name : 'غير محدد';
                            })
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('يتم تعيين المستخدم الحالي تلقائياً'),
                        
                        Hidden::make('marked_by')
                            ->default(function () {
                                return auth()->id();
                            }),
                    ])->columnSpan('full')->columns(3),
                
                Section::make('ملاحظات إضافية')
                    ->description('ملاحظات حول الحضور')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columnSpan('full')->columns(1),
            ]);
    }
}
