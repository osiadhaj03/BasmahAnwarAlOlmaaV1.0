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
                            ->label(' رقم الهاتف ')
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
                            ->default('student')
                            ->reactive()
                            ->live(),
                        
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
                        
                        Select::make('is_active')
                            ->label('نشط')
                            ->options([
                                true => 'نشط',
                                false => 'غير نشط',
                            ])
                            ->default(true),
                    ]),
                
                Section::make('معلومات إضافية')
                    ->description('معلومات تفصيلية حسب نوع المستخدم')
                    ->schema([
                        // معلومات خاصة بالطلاب
                        TextInput::make('student_id')
                            ->label(' رقم الطالب الجامعي')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            //->required(fn ($get) => $get('type') === 'student')
                            ->visible(fn ($get) => $get('type') === 'student')
                            ->placeholder('ادخل رقمك الجامعي إن كنت من طلاب كلية الفقه الحنفي '),
                        
                        Select::make('academic_level')
                            ->label('المستوى الأكاديمي')
                            ->options([
                                'bachelor' => 'بكالوريس',
                                'master' => 'ماجستير',
                                'doctorate' => 'دكتوراه',
                                'intermediate_diploma' => 'دبلوم متوسط',
                                'higher_diploma' => 'دبلوم عالي',
                                'other' => 'أخرى',
                            ])
                            ->visible(fn ($get) => $get('type') === 'student'),
                        
                        // معلومات خاصة بالمعلمين والمديرين
                        //TextInput::make('employee_id')
                        //    ->label('رقم الموظف')
                        //    ->unique(ignoreRecord: true)
                        //    ->maxLength(50)
                        //    ->required(fn ($get) => in_array($get('type'), ['admin', 'teacher']))
                        //    ->visible(fn ($get) => in_array($get('type'), ['admin', 'teacher'])),
                        
                        //TextInput::make('department')
                        //    ->label('القسم')
                        //    ->maxLength(100)
                        //    ->visible(fn ($get) => in_array($get('type'), ['admin', 'teacher'])),
                        
                        //Select::make('specialization')
                        //    ->label('التخصص')
                        //    ->options([
                        //        'arabic' => 'اللغة العربية',
                        //        'english' => 'اللغة الإنجليزية',
                        //        'math' => 'الرياضيات',
                        //        'science' => 'العلوم',
                        //        'physics' => 'الفيزياء',
                        //        'chemistry' => 'الكيمياء',
                        //        'biology' => 'الأحياء',
                        //        'history' => 'التاريخ',
                        //        'geography' => 'الجغرافيا',
                        //        'islamic' => 'التربية الإسلامية',
                        //        'computer' => 'الحاسوب',
                        //        'art' => 'التربية الفنية',
                        //        'sports' => 'التربية الرياضية',
                        //        'music' => 'التربية الموسيقية',
                        //        'other' => 'أخرى',
                        //    ])
                        //    ->visible(fn ($get) => $get('type') === 'teacher'),
                        
                        //DatePicker::make('hire_date')
                        //    ->label('تاريخ التوظيف')
                        //    ->visible(fn ($get) => in_array($get('type'), ['admin', 'teacher'])),
                        
                        // معلومات عامة
                        Textarea::make('bio')
                            ->label('نبذة شخصية')
                            ->maxLength(500)
                            ->rows(3),
                        
                        Textarea::make('address')
                            ->label('العنوان')
                            ->maxLength(500)
                            ->rows(2),
                        
                        TextInput::make('nationality')
                            ->label('الجنسية')
                            ->maxLength(100),
                            ]),
                
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
                        

                    ]),
            ]);
    }
}
