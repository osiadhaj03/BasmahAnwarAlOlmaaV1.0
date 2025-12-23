<?php

namespace App\Filament\Resources\KitchenInvoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KitchenInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subscription_id')
                    ->relationship('subscription', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('invoice_number')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('billing_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->required(),
                TextInput::make('collected_by')
                    ->numeric()
                    ->default(null),
                TextInput::make('received_from')
                    ->default(null),
                DateTimePicker::make('paid_at'),
                Select::make('payment_method')
                    ->options(['cash' => 'Cash', 'bank_transfer' => 'Bank transfer'])
                    ->default(null),
            ]);
    }
}
