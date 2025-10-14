<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Lesson;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                            ->preload(),
                        
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
                            ->label('تاريخ ووقت الحضور')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->default(now()),
                    ])->columns(2),
                
                Section::make('تفاصيل طريقة التسجيل')
                    ->description('معلومات حول كيفية تسجيل الحضور')
                    ->schema([
                        TextInput::make('used_code')
                            ->label('الكود المستخدم')
                            ->placeholder('الكود المستخدم في التسجيل')
                            ->maxLength(10),
                        
                        Select::make('attendance_method')
                            ->label('طريقة التسجيل')
                            ->options([
                                'code' => 'بالكود',
                                'manual' => 'يدوي',
                                'auto' => 'تلقائي',
                            ])
                            ->default('code')
                            ->required(),
                        
                        DateTimePicker::make('marked_at')
                            ->label('وقت التسجيل')
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->default(now()),
                        
                        Select::make('marked_by')
                            ->label('سجل بواسطة')
                            ->options(User::whereIn('type', ['teacher', 'admin'])->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                
                Section::make('ملاحظات إضافية')
                    ->description('ملاحظات حول الحضور')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }
}
