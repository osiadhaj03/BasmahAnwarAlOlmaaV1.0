<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('أدخل الاسم الكامل للطالب'),
                        
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('student@example.com'),
                        
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+966 50 123 4567'),
                        
                        DatePicker::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->maxDate(now()->subYears(5))
                            ->displayFormat('Y-m-d'),
                    ])
                    ->columns(2),
                
                Section::make('المعلومات الأكاديمية')
                    ->schema([
                        TextInput::make('student_id')
                            ->label('الرقم الجامعي')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('202312345')
                            ->helperText('الرقم الجامعي الفريد للطالب'),
                        
                        Select::make('level')
                            ->label('المستوى الدراسي')
                            ->options([
                                'freshman' => 'السنة الأولى',
                                'sophomore' => 'السنة الثانية',
                                'junior' => 'السنة الثالثة',
                                'senior' => 'السنة الرابعة',
                                'graduate' => 'دراسات عليا',
                            ])
                            ->placeholder('اختر المستوى الدراسي'),
                        
                        TextInput::make('major')
                            ->label('التخصص')
                            ->maxLength(100)
                            ->placeholder('علوم الحاسب'),
                        
                        Select::make('status')
                            ->label('حالة الطالب')
                            ->options([
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'graduated' => 'متخرج',
                                'suspended' => 'موقوف',
                                'transferred' => 'محول',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
                
                Section::make('معلومات الاتصال')
                    ->schema([
                        Textarea::make('address')
                            ->label('العنوان')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('أدخل العنوان الكامل'),
                        
                        TextInput::make('emergency_contact_name')
                            ->label('اسم جهة الاتصال في الطوارئ')
                            ->maxLength(255)
                            ->placeholder('اسم ولي الأمر أو المسؤول'),
                        
                        TextInput::make('emergency_contact_phone')
                            ->label('رقم هاتف الطوارئ')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+966 50 123 4567'),
                        
                        Select::make('emergency_contact_relation')
                            ->label('صلة القرابة')
                            ->options([
                                'father' => 'الأب',
                                'mother' => 'الأم',
                                'brother' => 'الأخ',
                                'sister' => 'الأخت',
                                'uncle' => 'العم',
                                'aunt' => 'العمة',
                                'guardian' => 'الوصي',
                                'other' => 'أخرى',
                            ])
                            ->placeholder('اختر صلة القرابة'),
                    ])
                    ->columns(2),
                
                Section::make('ملاحظات إضافية')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(1000)
                            ->rows(4)
                            ->placeholder('أي ملاحظات إضافية حول الطالب'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
