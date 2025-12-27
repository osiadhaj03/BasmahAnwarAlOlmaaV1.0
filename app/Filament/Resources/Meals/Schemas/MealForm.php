<?php

namespace App\Filament\Resources\Meals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // قسم معلومات الوجبة
                Section::make('معلومات الوجبة')
                    ->description('بيانات الوجبة الأساسية')
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم الوجبة')
                            ->required(),
                        Select::make('kitchen_id')
                            ->label('المطبخ')
                            ->relationship('kitchen', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('meal_type')
                            ->label('نوع الوجبة')
                            ->options([
                                'breakfast' => 'فطور',
                                'lunch' => 'غداء',
                                'dinner' => 'عشاء',
                            ])
                            ->required(),
                        DatePicker::make('meal_date')
                            ->label('تاريخ الوجبة')
                            ->required()
                            ->default(now()),
                    ])
                    ->columns(4)
                    ->columnSpan('full'),

                // قسم التفاصيل
                Section::make('تفاصيل إضافية')
                    ->description('الوصف والصورة')
                    ->schema([
                        Textarea::make('description')
                            ->label('الوصف')
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('صورة الوجبة')
                            ->image()
                            ->directory('meals')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
