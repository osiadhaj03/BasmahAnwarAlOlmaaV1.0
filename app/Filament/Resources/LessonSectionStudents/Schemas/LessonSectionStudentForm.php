<?php

namespace App\Filament\Resources\LessonSectionStudents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\LessonSection;
use App\Models\User;

class LessonSectionStudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات التسجيل')
                    ->description('قم بتحديد الطالب والقسم المراد تسجيله فيه')
                    ->schema([
                        Select::make('lesson_section_id')
                            ->label('قسم الدورة')
                            ->relationship('lessonSection', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('اختر القسم الذي تريد تسجيل الطالب فيه'),
                        
                        Select::make('student_id')
                            ->label('الطالب')
                            ->relationship('student', 'name', function ($query) {
                                return $query->where('type', 'student');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('اختر الطالب المراد تسجيله'),
                        
                        DateTimePicker::make('enrolled_at')
                            ->label('تاريخ التسجيل')
                            ->default(now())
                            ->required()
                            ->helperText('تاريخ ووقت تسجيل الطالب في القسم'),
                        
                        Select::make('enrollment_status')
                            ->label('حالة التسجيل')
                            ->options([
                                'active' => 'نشط',
                                'dropped' => 'متوقف',
                                'completed' => 'مكتمل'
                            ])
                            ->default('active')
                            ->required()
                            ->helperText('حالة تسجيل الطالب الحالية'),
                    ])
                    ->columns(2),
                
                Section::make('ملاحظات إضافية')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('أضف أي ملاحظات حول تسجيل الطالب...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
