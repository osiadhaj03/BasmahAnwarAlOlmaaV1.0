<?php

namespace App\Filament\Resources\KitchenInvoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->description('الربط بالاشتراك والمستخدم')
                    ->schema([
                        Select::make('subscription_id')
                            ->label('الاشتراك')
                            ->relationship('subscription', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name . ' - ' . $record->kitchen->name)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('user_id')
                            ->label('المستخدم')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('invoice_number')
                            ->label('رقم الفاتورة')
                            ->default(fn () => \App\Models\KitchenInvoice::generateInvoiceNumber())
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم المبلغ والتواريخ
                Section::make('المبلغ والتواريخ')
                    ->description('تفاصيل المبلغ وتواريخ الاستحقاق')
                    ->schema([
                        TextInput::make('amount')
                            ->label('المبلغ المطلوب')
                            ->required()
                            ->numeric()
                            ->prefix('د.أ'),
                        DatePicker::make('billing_date')
                            ->label('تاريخ الفوترة')
                            ->required()
                            ->default(now()),
                        DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->required()
                            ->default(now()->addDays(5)),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم حالة الفاتورة
                Section::make('حالة الفاتورة')
                    ->description('حالة الفاتورة الحالية')
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
                            ->required(),
                    ])
                    ->columns(1)
                    ->columnSpan('full'),
            ]);
    }
}
