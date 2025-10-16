<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Lesson;
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
                            ->label('الدرس')
                            ->options(Lesson::with('teacher')->get()->pluck('title_with_teacher', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $lesson = \App\Models\Lesson::find($state);
                                    if ($lesson) {
                                        $nextDateTime = $lesson->getNextLessonDateTime();
                                        if ($nextDateTime) {
                                            $set('attendance_date', $nextDateTime);
                                        }
                                    }
                                }
                            }),
                        
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
                                $lessonId = $get('lesson_id');
                                if ($lessonId) {
                                    $lesson = \App\Models\Lesson::find($lessonId);
                                    if ($lesson) {
                                        $bestDateTime = $lesson->getBestLessonDateTime();
                                        return $bestDateTime ?: now();
                                    }
                                }
                                return now();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $lessonId = $get('lesson_id');
                                if ($lessonId && !$state) {
                                    $lesson = \App\Models\Lesson::find($lessonId);
                                    if ($lesson) {
                                        $bestDateTime = $lesson->getBestLessonDateTime();
                                        if ($bestDateTime) {
                                            $set('attendance_date', $bestDateTime);
                                        }
                                    }
                                }
                            })
                            ->helperText('سيتم تعيين تاريخ ووقت الدرس الحالي إذا كان نشطاً، وإلا فالدرس القادم تلقائياً'),
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
