<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
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
                        
                        TextInput::make('location')
                            ->label('مكان الدرس')
                            ->maxLength(255),
                    ])->columns(2),
                
                Section::make('التوقيت والجدولة')
                    ->description('معلومات التوقيت والجدولة')
                    ->schema([
                        DatePicker::make('lesson_date')
                            ->label('تاريخ الدرس')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->minDate(now()),
                        
                        TimePicker::make('start_time')
                            ->label('وقت البداية')
                            ->required()
                            ->seconds(false),
                        
                        TimePicker::make('end_time')
                            ->label('وقت النهاية')
                            ->required()
                            ->seconds(false)
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
