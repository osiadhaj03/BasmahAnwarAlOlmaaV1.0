<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
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

                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->placeholder('أدخل كلمة مرور قوية')
                            ->helperText('يجب أن تكون كلمة المرور 8 أحرف على الأقل'),

                        TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور')
                            ->password()
                            ->required()
                            ->same('password')
                            ->placeholder('أعد إدخال كلمة المرور'),
                        
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
                    ])
                    ->columns(2),
                
                Section::make('معلومات الاتصال')
                    ->schema([
                        Textarea::make('address')
                            ->label('العنوان')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('أدخل العنوان الكامل'),
                    ])
                    ->columns(2),
            ]);
    }
}
