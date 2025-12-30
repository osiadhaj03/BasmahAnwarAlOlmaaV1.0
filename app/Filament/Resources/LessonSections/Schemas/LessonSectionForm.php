<?php

namespace App\Filament\Resources\LessonSections\Schemas;

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
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true)
                    ->required(),
                Toggle::make('is_mandatory')
                    ->label('إجبارية (تحسب الحضور والغياب)')
                    ->default(true)
                    ->helperText('إذا كانت اختيارية، لن يتم حساب الحضور والغياب لهذه الدورة')
                    ->required(),
                //TextInput::make('color')
                //    ->default(null),
                //TextInput::make('sort_order')
                //    ->required()
                //    ->numeric()
                //    ->default(0),
            ]);
    }
}
