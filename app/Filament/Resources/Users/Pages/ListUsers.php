<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->icon('heroicon-o-users'),
            'admin' => Tab::make('المدراء')
                ->icon('heroicon-o-cog-6-tooth')
                ->query(fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('slug', 'admin'))),
            'teacher' => Tab::make('المعلمين')
                ->icon('heroicon-o-academic-cap')
                ->query(fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('slug', 'teacher'))),
            'student' => Tab::make('الطلاب')
                ->icon('heroicon-o-user-group')
                ->query(fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('slug', 'student'))),
            'customer' => Tab::make('المشتركين')
                ->icon('heroicon-o-shopping-cart')
                ->query(fn ($query) => $query->where(function ($q) {
                    $q->whereHas('roles', fn ($r) => $r->where('slug', 'customer'))
                      ->orWhereHas('kitchenSubscriptions');
                })),
            'cook' => Tab::make('الطباخين')
                ->icon('heroicon-o-fire')
                ->query(fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('slug', 'cook'))),
        ];
    }
}
