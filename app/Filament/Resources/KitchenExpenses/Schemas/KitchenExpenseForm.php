<?php

namespace App\Filament\Resources\KitchenExpenses\Schemas;

use App\Models\ExpenseCategory;
use App\Models\Kitchen;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KitchenExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // قسم تفاصيل المصروف
                Section::make('تفاصيل المصروف')
                    ->description('معلومات المصروف الأساسية')
                    ->schema([
                        TextInput::make('title')
                            ->label('العنوان')
                            ->required(),
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('د.أ'),
                        DatePicker::make('expense_date')
                            ->label('التاريخ')
                            ->required()
                            ->default(now()),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('صورة الفاتورة/الإيصال')
                            ->image()
                            ->imageEditor()
                            ->directory('expenses')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم الربط
                Section::make('معلومات الربط')
                    ->description('ربط المصروف بالمطبخ والمورد والفئة')
                    ->schema([
                        Select::make('kitchen_id')
                            ->label('المطبخ')
                            ->relationship('kitchen', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('اسم المطبخ')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('location')
                                    ->label('الموقع')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->maxLength(20),
                                Textarea::make('description')
                                    ->label('الوصف')
                                    ->maxLength(500),
                                Toggle::make('is_active')
                                    ->label('نشط')
                                    ->default(true),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Kitchen::create($data)->id;
                            }),
                        
                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->default(null)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('اسم المورد')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('address')
                                    ->label('العنوان')
                                    ->maxLength(255),
                                Textarea::make('notes')
                                    ->label('ملاحظات')
                                    ->maxLength(500),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Supplier::create($data)->id;
                            }),
                        
                        Select::make('category_id')
                            ->label('الفئة')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->default(null)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('اسم الفئة')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('الوصف')
                                    ->maxLength(500),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return ExpenseCategory::create($data)->id;
                            }),
                        
                        // حقل ظاهر لعرض اسم المستخدم (للعرض فقط)
                        TextInput::make('creator_name')
                            ->label('أُنشئ بواسطة')
                            ->default(fn () => auth()->user()?->name)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(4)
                    ->columnSpan('full'),
            ]);
    }
}
