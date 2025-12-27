<?php

namespace App\Filament\Cook\Resources\CustomerGroups\Pages;

use App\Filament\Cook\Resources\CustomerGroups\CustomerGroupsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerGroups extends EditRecord
{
    protected static string $resource = CustomerGroupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
