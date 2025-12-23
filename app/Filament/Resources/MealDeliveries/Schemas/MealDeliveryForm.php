<?php

namespace App\Filament\Resources\MealDeliveries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MealDeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('meal_id')
                    ->relationship('meal', 'name')
                    ->default(null),
                TextInput::make('delivered_by')
                    ->numeric()
                    ->default(null),
                Select::make('subscription_id')
                    ->relationship('subscription', 'id')
                    ->required(),
                DatePicker::make('delivery_date')
                    ->required(),
                Select::make('meal_type')
                    ->options(['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'dinner' => 'Dinner'])
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'delivered' => 'Delivered', 'missed' => 'Missed'])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('delivered_at'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
