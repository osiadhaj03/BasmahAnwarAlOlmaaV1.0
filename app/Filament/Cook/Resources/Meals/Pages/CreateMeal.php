<?php

namespace App\Filament\Cook\Resources\Meals\Pages;

use App\Filament\Cook\Resources\Meals\MealResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeal extends CreateRecord
{
    protected static string $resource = MealResource::class;
}
