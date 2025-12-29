<?php

namespace App\Filament\Resources\KitchenInvoices\Schemas;

use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KitchenInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // قسم معلومات الفاتورة
                Section::make('معلومات الفاتورة')
                    ->description('اختر المستخدم وسيتم جلب الاشتراك الفعال تلقائياً')
                    ->schema([
                        // اختيار المستخدم - هذا الحقل الوحيد الذي يدخله المستخدم
                        Select::make('user_id')
                            ->label('المستخدم')
                            ->options(function () {
                                // جلب المستخدمين الذين لديهم اشتراكات فعالة فقط
                                return User::whereHas('kitchenSubscriptions', function ($query) {
                                    $query->where('status', 'active');
                                })->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state) {
                                    // جلب الاشتراك الفعال للمستخدم
                                    $activeSubscription = KitchenSubscription::where('user_id', $state)
                                        ->where('status', 'active')
                                        ->first();
                                    
                                    if ($activeSubscription) {
                                        $set('subscription_id', $activeSubscription->id);
                                        // تعيين المبلغ من سعر الاشتراك الشهري إذا موجود، وإلا 30 دينار
                                        $set('amount', $activeSubscription->monthly_price ?? 30);
                                    } else {
                                        $set('subscription_id', null);
                                        $set('amount', 30);
                                    }
                                }
                            }),

                        // الاشتراك - يتم تعبئته تلقائياً ولا يمكن تعديله (يعرض رقم الاشتراك)
                        Select::make('subscription_id')
                            ->label('الاشتراك')
                            ->options(function (Get $get) {
                                $userId = $get('user_id');
                                if (!$userId) {
                                    return [];
                                }
                                return KitchenSubscription::where('user_id', $userId)
                                    ->where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(fn ($sub) => [
                                        $sub->id => ($sub->subscription_number ?? 'بدون رقم') . ' - ' . $sub->kitchen->name
                                    ]);
                            })
                            ->disabled() // لا يمكن تعديله
                            ->dehydrated() // لكن يتم إرسال القيمة
                            ->required(),

                        // رقم الفاتورة - تلقائي ولا يمكن تعديله
                        TextInput::make('invoice_number')
                            ->label('رقم الفاتورة')
                            ->default(fn () => KitchenInvoice::generateInvoiceNumber())
                            ->disabled() // لا يمكن تعديله
                            ->dehydrated() // لكن يتم إرسال القيمة
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم المبلغ والتواريخ
                Section::make('المبلغ والتواريخ')
                    ->description('المبلغ الافتراضي 30 دينار وتاريخ الاستحقاق أول الشهر')
                    ->schema([
                        TextInput::make('amount')
                            ->label('المبلغ المطلوب')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->prefix('د.أ')
                            ->helperText('المبلغ الافتراضي 30 دينار، يمكنك تغييره'),

                        DatePicker::make('billing_date')
                            ->label('تاريخ الفوترة')
                            ->required()
                            ->default(now()),

                        // تاريخ الاستحقاق - أول الشهر القادم تلقائياً
                        DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->required()
                            ->default(fn () => now()->addMonth()->startOfMonth())
                            ->helperText('تلقائياً أول الشهر القادم'),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم حالة الفاتورة
                Section::make('حالة الفاتورة')
                    ->description('الحالة تتغير تلقائياً بناءً على الدفعات')
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'paid' => 'مدفوعة',
                                'partial' => 'مدفوعة جزئياً',
                                'overdue' => 'متأخرة',
                                'cancelled' => 'ملغاة',
                            ])
                            ->default('pending')
                            ->disabled() // لا يمكن تعديله يدوياً
                            ->dehydrated()
                            ->helperText('تتغير تلقائياً بناءً على الدفعات المسجلة'),

                        // عرض معلومات الدفعات
                        Placeholder::make('payment_info')
                            ->label('معلومات الدفع')
                            ->content(function (?KitchenInvoice $record): string {
                                if (!$record) {
                                    return 'لم يتم حفظ الفاتورة بعد';
                                }
                                
                                $totalPaid = $record->total_paid;
                                $remaining = $record->remaining_amount;
                                
                                return "المدفوع: {$totalPaid} د.أ | المتبقي: {$remaining} د.أ";
                            })
                            ->visibleOn(['edit', 'view']),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}

