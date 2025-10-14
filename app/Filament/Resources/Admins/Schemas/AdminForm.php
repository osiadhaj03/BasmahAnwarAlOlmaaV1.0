<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->description('المعلومات الأساسية للإداري')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('أدخل الاسم الكامل'),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('admin@example.com'),

                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+966 50 123 4567'),

                        DatePicker::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->maxDate(now()->subYears(18)),
                    ]),

                Section::make('بيانات الاعتماد')
                    ->description('كلمة المرور ومعلومات الحساب')
                    ->icon('heroicon-o-key')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->placeholder('أدخل كلمة مرور قوية'),

                        TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->same('password')
                            ->dehydrated(false)
                            ->placeholder('أعد إدخال كلمة المرور'),

                        Hidden::make('role')
                            ->default('admin'),

                        Toggle::make('is_active')
                            ->label('الحساب نشط')
                            ->default(true)
                            ->helperText('تحديد ما إذا كان بإمكان الإداري تسجيل الدخول'),
                    ]),

                Section::make('الصلاحيات والمسؤوليات')
                    ->description('تحديد صلاحيات ومسؤوليات الإداري')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        Select::make('department')
                            ->label('القسم')
                            ->options([
                                'academic' => 'الشؤون الأكاديمية',
                                'student_affairs' => 'شؤون الطلاب',
                                'hr' => 'الموارد البشرية',
                                'finance' => 'الشؤون المالية',
                                'it' => 'تقنية المعلومات',
                                'general' => 'الإدارة العامة',
                            ])
                            ->native(false)
                            ->placeholder('اختر القسم'),

                        TextInput::make('employee_id')
                            ->label('رقم الموظف')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('EMP001'),
                    ]),

                Section::make('معلومات الاتصال')
                    ->description('معلومات الاتصال والعنوان')
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
