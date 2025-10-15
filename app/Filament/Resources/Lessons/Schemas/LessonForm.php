<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Models\LessonSection;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Get;

use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الدورة الأساسية')
                    ->description('المعلومات الأساسية للدورة')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان الدورة')
                            ->required()
                            ->maxLength(255),
                        
                        Select::make('lesson_section_id')
                            ->label('قسم الدورة')
                            ->options(LessonSection::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('اختر قسم الدورة')
                            ->helperText('اختر القسم الذي ينتمي إليه هذه الدورة'),
                        
                        Textarea::make('description')
                            ->label('وصف الدورة')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Select::make('teacher_id')
                            ->label('المعلم')
                            ->options(User::where('type', 'teacher')->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                
                Section::make('التوقيت والجدولة')
                    ->description('معلومات التوقيت والجدولة')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('تاريخ البداية')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->minDate(now()),
                        
                        DatePicker::make('end_date')
                            ->label('تاريخ النهاية')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->minDate(now())
                            ->afterOrEqual('start_date'),
                        
                        CheckboxList::make('lesson_days')
                            ->label('أيام الدورة')
                            ->options([
                                'sunday' => 'الأحد',
                                'monday' => 'الاثنين',
                                'tuesday' => 'الثلاثاء',
                                'wednesday' => 'الأربعاء',
                                'thursday' => 'الخميس',
                                'friday' => 'الجمعة',
                                'saturday' => 'السبت',
                            ])
                            ->columns(3)
                            ->required()
                            ->helperText('اختر أيام الأسبوع التي سيتم فيها الدورة'),
                        
                        TimePicker::make('start_time')
                            ->label('وقت البداية')
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i'),
                        
                        TimePicker::make('end_time')
                            ->label('وقت النهاية')
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i')
                            ->after('start_time'),
                        
                        Select::make('status')
                            ->label('حالة الدورة')
                            ->options([
                                'scheduled' => 'مجدول',
                                'in_progress' => 'جاري',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                            ])
                            ->default('scheduled')
                            ->required(),
                        
                        Toggle::make('is_recurring')
                            ->label('الدورة متكررة')
                            ->helperText('هل هذه الدورة تتكرر بانتظام؟')
                            ->default(false),
                     ])->columns(2),
                
                Section::make('المكان والموقع')
                    ->description('تفاصيل مكان إقامة الدورة')
                    ->schema([
                        Select::make('location_type')
                            ->label('نوع المكان')
                            ->options([
                                'online' => 'أونلاين',
                                'offline' => 'حضوري',
                            ])
                            ->default('offline')
                            ->required()
                            ->live(),
                        
                    ])->columns(2),
                
                Section::make('إعدادات إضافية')
                    ->description('إعدادات وملاحظات إضافية')
                    ->schema([
                        TextInput::make('max_students')
                            ->label('العدد الأقصى للطلاب')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            
                            ->helperText('العدد الأقصى للطلاب في الدورة'),
                        
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(1000)
                            ->rows(3)
                            ->helperText('ملاحظات إضافية حول الدورة')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }
}
