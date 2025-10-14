<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الأساسية')
                    ->description('المعلومات الشخصية الأساسية للمستخدم')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(20),
                        
                        Select::make('type')
                            ->label('نوع المستخدم')
                            ->options([
                                'admin' => 'مدير',
                                'teacher' => 'معلم',
                                'student' => 'طالب',
                            ])
                            ->required()
                            ->default('student'),
                        
                        Select::make('gender')
                            ->label('الجنس')
                            ->options([
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ])
                            ->nullable(),
                        
                        DatePicker::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->nullable(),
                        
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])->columns(2),
                
                Section::make('معلومات إضافية')
                    ->description('معلومات تفصيلية اختيارية')
                    ->schema([
                        TextInput::make('student_id')
                            ->label('رقم الطالب')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->visible(fn ($get) => $get('type') === 'student'),
                        
                        TextInput::make('employee_id')
                            ->label('رقم الموظف')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->visible(fn ($get) => in_array($get('type'), ['admin', 'teacher'])),
                        
                        TextInput::make('department')
                            ->label('القسم')
                            ->maxLength(100),
                        
                        Textarea::make('bio')
                            ->label('نبذة شخصية')
                            ->maxLength(500)
                            ->rows(3),
                        
                        Textarea::make('address')
                            ->label('العنوان')
                            ->maxLength(500)
                            ->rows(2),
                    ])->columns(2),
                
                Section::make('كلمة المرور')
                    ->description('إعدادات كلمة المرور والأمان')
                    ->schema([
                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->dehydrated(fn ($state): bool => filled($state)),
                        
                        TextInput::make('passwordConfirmation')
                            ->label('تأكيد كلمة المرور')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(false),
                        
                        DateTimePicker::make('email_verified_at')
                            ->label('تاريخ تأكيد البريد الإلكتروني')
                            ->nullable(),
                        
                        DateTimePicker::make('last_login_at')
                            ->label('آخر تسجيل دخول')
                            ->disabled()
                            ->nullable(),
                    ])->columns(2),
            ]);
    }
}
