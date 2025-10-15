<?php

namespace App\Filament\Resources\LessonSections\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات القسم الأساسية')

                    ->description('أدخل المعلومات الأساسية لقسم الدروس')
                    ->schema([
                        
                        TextInput::make('name')
                                    ->label('اسم القسم')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('مثال: الفقه، التفسير، الحديث')
                                    ->columnSpan(1),
                                
                        Toggle::make('is_active')
                                    ->label('نشط')
                                    ->default(true)
                                    ->helperText('تفعيل أو إلغاء تفعيل القسم')
                                    ->columnSpan(1),
                        
                        
                        Textarea::make('description')
                            ->label('وصف القسم')
                            ->placeholder('وصف مختصر عن محتوى هذا القسم من الدروس')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                
            ]);
    }
}
