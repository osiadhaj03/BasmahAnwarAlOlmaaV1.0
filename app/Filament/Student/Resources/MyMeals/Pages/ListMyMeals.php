<?php

namespace App\Filament\Student\Resources\MyMeals\Pages;

use App\Filament\Student\Resources\MyMeals\MyMealsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyMeals extends ListRecords
{
    protected static string $resource = MyMealsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
