<?php

namespace App\Filament\Cook\Resources\CustomerGroups\Pages;

use App\Filament\Cook\Resources\CustomerGroups\CustomerGroupsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerGroups extends CreateRecord
{
    protected static string $resource = CustomerGroupsResource::class;
}
