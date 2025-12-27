<?php

namespace App\Filament\Resources\KitchenPayments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KitchenPaymentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // قسم معلومات الدفعة
                Section::make('معلومات الدفعة')
                    ->description('ربط الدفعة بالاشتراك والفاتورة')
                    ->schema([
                        Select::make('subscription_id')
                            ->label('الاشتراك')
                            ->relationship('subscription', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name . ' - ' . $record->kitchen->name)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('invoice_id')
                            ->label('الفاتورة (اختياري)')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload()
                            ->default(null),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                // قسم تفاصيل الدفع
                Section::make('تفاصيل الدفع')
                    ->description('المبلغ وتاريخ الدفع')
                    ->schema([
                        TextInput::make('amount')
                            ->label('المبلغ المدفوع')
                            ->required()
                            ->numeric()
                            ->prefix('د.أ'),
                        DatePicker::make('payment_date')
                            ->label('تاريخ الدفع')
                            ->required()
                            ->default(now()),
                        Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options([
                                'cash' => 'نقداً',
                                'bank_transfer' => 'تحويل بنكي',
                            ])
                            ->default('cash')
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم معلومات التحصيل
                Section::make('معلومات التحصيل')
                    ->description('من قام بالتحصيل والملاحظات')
                    ->schema([
                        Select::make('collected_by')
                            ->label('المحصّل')
                            ->relationship('collector', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
