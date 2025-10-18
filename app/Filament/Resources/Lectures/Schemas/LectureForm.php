<?php

namespace App\Filament\Resources\Lectures\Schemas;

use App\Models\Lesson;
 use App\Models\Lecture;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LectureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات المحاضرة الأساسية')
                    ->description('المعلومات الأساسية للمحاضرة')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان المحاضرة')
                            ->required()
                            ->maxLength(255),
                        Select::make('lesson_id')
                            ->label('الدورة')
                            ->options(Lesson::all()->pluck('title', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('lecture_number')
                            ->label('رقم المحاضرة')
                            ->numeric()
                            ->default(function () {
                                return Lecture::max('lecture_number') + 1 ?? 1;
                            })
                            ->required()
                            ->minValue(1),

                        Textarea::make('description')
                            ->label('وصف المحاضرة')
                            ->rows(3)
                            ->columnSpanFull()
                            ->columnSpanFull(),

                        
                    ])->columnSpan('full')->columns(3),

                Section::make('التوقيت والمكان')
                    ->description('معلومات التوقيت والمكان')
                    ->schema([
                        DateTimePicker::make('lecture_date')
                            ->label('تاريخ ووقت المحاضرة')
                            ->seconds(false),

                        TextInput::make('duration_minutes')
                            ->label('مدة المحاضرة (بالدقائق)')
                            ->numeric()
                            ->default(60)
                            ->required()
                            ->minValue(1),

                        Select::make('location')
                            ->label('مكان المحاضرة')
                            ->options([
                                'وجاهي' => 'وجاهي',
                                'اونلاين' => 'اونلاين',
                            ])
                            ->default('وجاهي')
                            ->required(),


                    ])->columnSpan('full')
                    ->columns(2),

                Section::make('إعدادات إضافية')
                    ->description('الإعدادات والملاحظات')
                    ->schema([
                        //Toggle::make('is_mandatory')
                        //    ->label('محاضرة إجبارية')
                        //    ->default(true),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),

                        //TextInput::make('recording_url')
                        //    ->label('رابط تسجيل المحاضرة')
                        //    ->url()
                        //    ->maxLength(255)
                        //    ->columnSpanFull(),
                    ])->columnSpan('full')
                    ->columns(2),
            ]);
    }
}
