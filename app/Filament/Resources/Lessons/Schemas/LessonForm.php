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
                            ->maxLength(255)
                            ->columnSpan('full'),
                        
                        Select::make('lesson_section_id')
                            ->label('قسم الدورة')
                            ->options(LessonSection::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('اختر قسم الدورة')
                            ->helperText('اختر القسم الذي ينتمي إليه هذه الدورة'),

                        Select::make('teacher_id')
                            ->label('المعلم')
                            ->options(User::where('type', 'teacher')->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('location_type')
                            ->label('نوع المكان')
                            ->options([
                                'online' => 'أونلاين',
                                'offline' => 'حضوري',
                            ])
                            ->default('offline')
                            ->required()
                            ->live(),    

                        Textarea::make('description')
                            ->label('وصف الدورة')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpan('full'),
                        
                        
                    ]) ->columnSpan('full') ->columns(3),
                
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
                            ->columns(4)
                            ->required()
                            ->helperText('اختر أيام الأسبوع التي سيتم فيها الدورة')
                            ->columnSpan('full'),
                        
                        

                        TextInput::make('max_students')
                            ->label('العدد الأقصى للطلاب')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            
                            ->helperText('العدد الأقصى للطلاب في الدورة'),
                        
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
                        
                        //Toggle::make('is_recurring')
                        //    ->label('الدورة متكررة')
                        //    ->helperText('هل هذه الدورة تتكرر بانتظام؟')
                        //    ->default(false),
                     ])->columnSpan('full')->columns(4),
                

                Section::make('إعدادات إضافية')
                    ->description('إعدادات وملاحظات إضافية')
                    ->schema([
                        
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(1000)
                            ->rows(3)
                            ->helperText('ملاحظات إضافية حول الدورة')
                            ->columnSpanFull()
                            ->columnSpan('full'),
                    ])->columnSpan('full'),
            ]);
    }
}
