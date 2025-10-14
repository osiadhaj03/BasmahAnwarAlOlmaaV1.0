<?php

namespace App\Filament\Resources\AttendanceCodes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttendanceCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الكود الأساسية')
                    ->schema([
                        Select::make('lesson_id')
                            ->label('الدرس')
                            ->relationship('lesson', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('اختر الدرس'),
                        
                        TextInput::make('code')
                            ->label('الكود')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('أدخل الكود')
                            ->helperText('كود فريد للحضور'),
                        
                        DateTimePicker::make('expires_at')
                            ->label('تاريخ انتهاء الصلاحية')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->helperText('متى ينتهي صلاحية هذا الكود'),
                        
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->helperText('هل الكود نشط ويمكن استخدامه'),
                    ])
                    ->columns(2),
                
                Section::make('إعدادات الاستخدام')
                    ->schema([
                        TextInput::make('max_usage')
                            ->label('الحد الأقصى للاستخدام')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('غير محدود')
                            ->helperText('اتركه فارغاً للاستخدام غير المحدود'),
                        
                        TextInput::make('usage_count')
                            ->label('عدد مرات الاستخدام')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('عدد المرات التي تم استخدام الكود فيها'),
                    ])
                    ->columns(2),
                
                Section::make('معلومات إضافية')
                    ->schema([
                        Hidden::make('created_by')
                            ->default(auth()->id()),
                        
                        DateTimePicker::make('deactivated_at')
                            ->label('تاريخ إلغاء التفعيل')
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->placeholder('لم يتم إلغاء التفعيل'),
                        
                        Select::make('deactivated_by')
                            ->label('ألغى التفعيل')
                            ->relationship('deactivatedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('لم يتم إلغاء التفعيل'),
                        
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('أي ملاحظات إضافية حول الكود')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }
}
