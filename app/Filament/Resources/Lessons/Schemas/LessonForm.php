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

use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الدرس الأساسية')
                    ->description('المعلومات الأساسية للدرس')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان الدرس')
                            ->required()
                            ->maxLength(255),
                        
                        Select::make('lesson_section_id')
                            ->label('قسم الدرس')
                            ->options(LessonSection::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('اختر قسم الدرس')
                            ->helperText('اختر القسم الذي ينتمي إليه هذا الدرس'),
                        
                        Textarea::make('description')
                            ->label('وصف الدرس')
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
                            ->label('أيام الدرس')
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
                            ->helperText('اختر أيام الأسبوع التي سيتم فيها الدرس'),
                        
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
                            ->label('حالة الدرس')
                            ->options([
                                'active' => 'نشط',
                                'cancelled' => 'ملغي',
                                'completed' => 'مكتمل',
                            ])
                            ->default('active')
                            ->required(),
                        
                        Toggle::make('is_recurring')
                            ->label('درس متكرر')
                            ->default(true)
                            ->helperText('هل هذا الدرس متكرر أم لمرة واحدة فقط؟'),
                     ])->columns(2),
                
                Section::make('المكان والموقع')
                    ->description('تحديد مكان إقامة الدرس')
                    ->schema([
                        Select::make('location_type')
                            ->label('نوع المكان')
                            ->options([
                                'online' => 'أونلاين',
                                'offline' => 'حضوري',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('location_details', null)),
                        
                        TextInput::make('location_details')
                            ->label(fn (callable $get) => $get('location_type') === 'online' ? 'تفاصيل المنصة' : 'عنوان المكان')
                            ->placeholder(fn (callable $get) => $get('location_type') === 'online' ? 'مثل: Zoom, Teams, Google Meet' : 'أدخل عنوان المكان')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => filled($get('location_type'))),
                        
                        TextInput::make('meeting_link')
                            ->label('رابط الاجتماع')
                            ->url()
                            ->placeholder('https://zoom.us/j/...')
                            ->maxLength(500)
                            ->visible(fn (callable $get) => $get('location_type') === 'online')
                            ->helperText('رابط الاجتماع للدروس الأونلاين'),
                    ])->columns(2),
                
                Section::make('إعدادات إضافية')
                    ->description('إعدادات وملاحظات إضافية')
                    ->schema([
                        TextInput::make('max_students')
                            ->label('الحد الأقصى للطلاب')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->placeholder('غير محدود'),
                        
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }
}
