<?php

namespace App\Filament\Resources\Subscribers\Pages;

use App\Filament\Resources\Subscribers\SubscriberResource;
use App\Models\MealDelivery;
use App\Models\User;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // لا إجراء إنشاء - يتم من إدارة الاشتراكات
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->icon('heroicon-o-users')
                ->badge($this->getAllCount()),

            'delivered' => Tab::make('تم التسليم')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => 
                    $query->whereHas('mealDeliveries', fn ($q) => 
                        $q->whereDate('delivery_date', today())
                          ->where('status', 'delivered')
                    )
                )
                ->badge($this->getDeliveredCount())
                ->badgeColor('success'),

            'pending' => Tab::make('لم يستلم')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn ($query) => 
                    $query->whereDoesntHave('mealDeliveries', fn ($q) => 
                        $q->whereDate('delivery_date', today())
                          ->where('status', 'delivered')
                    )
                )
                ->badge($this->getPendingCount())
                ->badgeColor('warning'),
        ];
    }

    protected function getAllCount(): int
    {
        return User::whereHas('kitchenSubscriptions', fn ($q) => $q->where('status', 'active'))->count();
    }

    protected function getDeliveredCount(): int
    {
        return User::whereHas('kitchenSubscriptions', fn ($q) => $q->where('status', 'active'))
            ->whereHas('mealDeliveries', fn ($q) => 
                $q->whereDate('delivery_date', today())
                  ->where('status', 'delivered')
            )
            ->count();
    }

    protected function getPendingCount(): int
    {
        return User::whereHas('kitchenSubscriptions', fn ($q) => $q->where('status', 'active'))
            ->whereDoesntHave('mealDeliveries', fn ($q) => 
                $q->whereDate('delivery_date', today())
                  ->where('status', 'delivered')
            )
            ->count();
    }
}
