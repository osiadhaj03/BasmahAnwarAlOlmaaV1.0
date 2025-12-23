<?php

namespace App\Filament\Resources\Meals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kitchen_id')
                    ->relationship('kitchen', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('meal_type')
                    ->options(['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'dinner' => 'Dinner'])
                    ->required(),
                FileUpload::make('image')
                    ->image(),
            ]);
    }
}
