<?php

namespace App\Filament\Resources\MealDeliveries\Schemas;

use App\Models\KitchenSubscription;
use App\Models\Meal;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MealDeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // قسم معلومات التسليم
                Section::make('معلومات التسليم')
                    ->description('اختر المشترك لجلب الاشتراك والوجبة تلقائياً')
                    ->schema([
                        Select::make('user_id')
                            ->label('المشترك')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($state) {
                                    // جلب الاشتراك النشط للمستخدم
                                    $subscription = KitchenSubscription::where('user_id', $state)
                                        ->where('status', 'active')
                                        ->first();
                                    
                                    if ($subscription) {
                                        $set('subscription_id', $subscription->id);
                                        
                                        // جلب وجبة اليوم من المطبخ
                                        $todayMeal = Meal::where('kitchen_id', $subscription->kitchen_id)
                                            ->whereDate('meal_date', today())
                                            ->first();
                                        
                                        if ($todayMeal) {
                                            $set('meal_id', $todayMeal->id);
                                            $set('meal_type', $todayMeal->meal_type);
                                            $set('delivery_date', today()->toDateString());
                                        }
                                    }
                                }
                            }),
                        Select::make('subscription_id')
                            ->label('الاشتراك')
                            ->relationship('subscription', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name . ' - ' . $record->kitchen->name)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('meal_id')
                            ->label('الوجبة')
                            ->relationship('meal', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->meal_date->format('Y-m-d') . ')')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم حالة التسليم
                Section::make('حالة التسليم')
                    ->description('تفاصيل عملية التسليم')
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'delivered' => 'تم التسليم',
                                'missed' => 'فائت',
                            ])
                            ->default('delivered')
                            ->required(),
                        Select::make('delivered_by')
                            ->label('المُسلّم')
                            ->relationship('deliveredBy', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),
                        DateTimePicker::make('delivered_at')
                            ->label('وقت التسليم')
                            ->default(now()),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم ملاحظات
                Section::make('ملاحظات')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->default(null)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan('full')
                    ->collapsed(),

                // حقول مخفية (تُملأ تلقائياً)
                \Filament\Forms\Components\Hidden::make('meal_type'),
                \Filament\Forms\Components\Hidden::make('delivery_date')
                    ->default(today()->toDateString()),
            ]);
    }
}
