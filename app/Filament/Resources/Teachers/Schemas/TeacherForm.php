<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->description('المعلومات الأساسية للمعلم')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('أدخل الاسم الكامل للمعلم'),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('example@domain.com'),

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
                            ->maxDate(now()->subYears(18))
                            ->displayFormat('Y-m-d'),
                    ]),

                Section::make('المعلومات المهنية')
                    ->description('المعلومات المتعلقة بالعمل والتخصص')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        TextInput::make('employee_id')
                            ->label('رقم الموظف')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('EMP001'),

                        Select::make('department')
                            ->label('القسم')
                            ->options([
                                'computer_science' => 'علوم الحاسوب',
                                'mathematics' => 'الرياضيات',
                                'physics' => 'الفيزياء',
                                'chemistry' => 'الكيمياء',
                                'biology' => 'الأحياء',
                                'english' => 'اللغة الإنجليزية',
                                'arabic' => 'اللغة العربية',
                                'history' => 'التاريخ',
                                'geography' => 'الجغرافيا',
                                'islamic_studies' => 'الدراسات الإسلامية',
                                'other' => 'أخرى',
                            ])
                            ->searchable()
                            ->placeholder('اختر القسم'),


                    ]),

                Section::make('معلومات الاتصال')
                    ->description('معلومات العنوان والاتصال')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->label('العنوان')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('أدخل العنوان الكامل'),

                    ]),
            ]);
    }
}
