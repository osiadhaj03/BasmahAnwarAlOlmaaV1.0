<?php

namespace App\Filament\Cook\Resources\CustomerGroups\Pages;

use App\Filament\Cook\Resources\CustomerGroups\CustomerGroupsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerGroups extends ListRecords
{
    protected static string $resource = CustomerGroupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
