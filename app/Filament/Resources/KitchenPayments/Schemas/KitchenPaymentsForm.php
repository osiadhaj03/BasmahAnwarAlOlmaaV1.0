<?php

namespace App\Filament\Resources\KitchenPayments\Schemas;

use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class KitchenPaymentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // قسم اختيار المستخدم
                Section::make('اختيار المشترك')
                    ->schema([
                        // اختيار المستخدم أولاً
                        Select::make('user_id_selector')
                            ->label('المشترك')
                            ->options(function () {
                                // جلب المستخدمين الذين لديهم اشتراكات فعالة
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
                                        $set('subscription_number_display', $activeSubscription->subscription_number ?? 'بدون رقم');
                                        // عرض رصيد المحفظة
                                        $set('credit_balance_display', number_format($activeSubscription->credit_balance ?? 0, 2) . ' د.أ');
                                    } else {
                                        $set('subscription_id', null);
                                        $set('subscription_number_display', null);
                                        $set('credit_balance_display', '0.00 د.أ');
                                    }
                                    
                                    // إعادة تعيين الفاتورة المختارة
                                    $set('invoice_id', null);
                                }
                            }),

                        // رقم الاشتراك - للعرض فقط
                        TextInput::make('subscription_number_display')
                            ->label('رقم الاشتراك')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('سيظهر عند اختيار المشترك'),

                        // رصيد المحفظة - للعرض فقط
                        TextInput::make('credit_balance_display')
                            ->label('💰 رصيد المحفظة')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('0.00 د.أ')
                            ->helperText('الرصيد المتاح - سيتم خصمه تلقائياً من الفواتير الجديدة'),

                        // حقل مخفي للاشتراك
                        \Filament\Forms\Components\Hidden::make('subscription_id'),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),

                // قسم ملخص الفواتير المستحقة
                Section::make('الفواتير المستحقة')
                    ->schema([
                        // عرض جدول الفواتير المستحقة
                        Placeholder::make('invoices_summary')
                            ->label('')
                            ->content(function (Get $get): HtmlString {
                                $userId = $get('user_id_selector');
                                
                                if (!$userId) {
                                    return new HtmlString('<div class="text-gray-500 text-center py-4">اختر المشترك لعرض الفواتير المستحقة</div>');
                                }
                                
                                // جلب الفواتير غير المدفوعة بالكامل
                                $invoices = KitchenInvoice::where('user_id', $userId)
                                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                                    ->get();
                                
                                if ($invoices->isEmpty()) {
                                    return new HtmlString('<div class="text-green-600 text-center py-4 font-bold">🎉 لا توجد فواتير مستحقة - جميع الفواتير مدفوعة</div>');
                                }
                                
                                // بناء جدول HTML مع حدود واضحة
                                $html = '<div class="overflow-x-auto rounded-lg border border-gray-300 dark:border-gray-600">';
                                $html .= '<table class="w-full text-sm" style="border-collapse: collapse;">';
                                $html .= '<thead>';
                                $html .= '<tr style="background-color: #f3f4f6;">';
                                $html .= '<th style="border: 2px solid #d1d5db; padding: 12px; text-align: right; font-weight: bold;">رقم الفاتورة</th>';
                                $html .= '<th style="border: 2px solid #d1d5db; padding: 12px; text-align: right; font-weight: bold;">المبلغ الكلي</th>';
                                $html .= '<th style="border: 2px solid #d1d5db; padding: 12px; text-align: right; font-weight: bold;">المدفوع</th>';
                                $html .= '<th style="border: 2px solid #d1d5db; padding: 12px; text-align: right; font-weight: bold;">المتبقي</th>';
                                $html .= '<th style="border: 2px solid #d1d5db; padding: 12px; text-align: right; font-weight: bold;">الحالة</th>';
                                $html .= '<th style="border: 2px solid #d1d5db; padding: 12px; text-align: right; font-weight: bold;">تاريخ الاستحقاق</th>';
                                $html .= '</tr>';
                                $html .= '</thead>';
                                $html .= '<tbody>';
                                
                                $totalAmount = 0;
                                $totalPaid = 0;
                                $totalRemaining = 0;
                                
                                foreach ($invoices as $invoice) {
                                    $paid = $invoice->total_paid;
                                    $remaining = $invoice->remaining_amount;
                                    
                                    $totalAmount += $invoice->amount;
                                    $totalPaid += $paid;
                                    $totalRemaining += $remaining;
                                    
                                    // تحديد لون الحالة
                                    $statusStyle = match($invoice->status) {
                                        'overdue' => 'color: #dc2626; font-weight: bold;',
                                        'partial' => 'color: #d97706;',
                                        default => 'color: #4b5563;',
                                    };
                                    
                                    $html .= '<tr style="background-color: #ffffff;">';
                                    $html .= '<td style="border: 1px solid #d1d5db; padding: 10px; text-align: right;">' . $invoice->invoice_number . '</td>';
                                    $html .= '<td style="border: 1px solid #d1d5db; padding: 10px; text-align: right;">' . number_format($invoice->amount, 2) . ' د.أ</td>';
                                    $html .= '<td style="border: 1px solid #d1d5db; padding: 10px; text-align: right; color: #16a34a;">' . number_format($paid, 2) . ' د.أ</td>';
                                    $html .= '<td style="border: 1px solid #d1d5db; padding: 10px; text-align: right; color: #dc2626; font-weight: bold;">' . number_format($remaining, 2) . ' د.أ</td>';
                                    $html .= '<td style="border: 1px solid #d1d5db; padding: 10px; text-align: right; ' . $statusStyle . '">' . $invoice->status_arabic . '</td>';
                                    $html .= '<td style="border: 1px solid #d1d5db; padding: 10px; text-align: right;">' . $invoice->due_date->format('Y-m-d') . '</td>';
                                    $html .= '</tr>';
                                }
                                
                                // صف المجموع
                                $html .= '<tr style="background-color: #e5e7eb; font-weight: bold;">';
                                $html .= '<td style="border: 2px solid #9ca3af; padding: 12px; text-align: right;">المجموع</td>';
                                $html .= '<td style="border: 2px solid #9ca3af; padding: 12px; text-align: right;">' . number_format($totalAmount, 2) . ' د.أ</td>';
                                $html .= '<td style="border: 2px solid #9ca3af; padding: 12px; text-align: right; color: #16a34a;">' . number_format($totalPaid, 2) . ' د.أ</td>';
                                $html .= '<td style="border: 2px solid #9ca3af; padding: 12px; text-align: right; color: #dc2626;">' . number_format($totalRemaining, 2) . ' د.أ</td>';
                                $html .= '<td style="border: 2px solid #9ca3af; padding: 12px;" colspan="2"></td>';
                                $html .= '</tr>';
                                
                                $html .= '</tbody>';
                                $html .= '</table>';
                                $html .= '</div>';
                            
                                
                                return new HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpan('full')
                    ->visible(fn (Get $get) => $get('user_id_selector') !== null),

                // قسم تفاصيل الدفعة
                Section::make('تفاصيل الدفعة')
                    ->schema([
                        // اختيار الفاتورة (فواتير غير مدفوعة فقط)
                        Select::make('invoice_id')
                            ->label('الفاتورة المراد الدفع لها')
                            ->options(function (Get $get) {
                                $userId = $get('user_id_selector');
                                if (!$userId) {
                                    return [];
                                }
                                return KitchenInvoice::where('user_id', $userId)
                                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                                    ->get()
                                    ->mapWithKeys(fn ($inv) => [
                                        $inv->id => $inv->invoice_number . ' - متبقي: ' . number_format($inv->remaining_amount, 2) . ' د.أ'
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state) {
                                    $invoice = KitchenInvoice::find($state);
                                    if ($invoice) {
                                        // تعيين المبلغ المتبقي كقيمة افتراضية
                                        $set('amount', $invoice->remaining_amount);
                                    }
                                }
                            }),

                        TextInput::make('amount')
                            ->label('المبلغ المدفوع')
                            ->required()
                            ->numeric()
                            ->prefix('د.أ')
                            ->helperText('يتم تعيين المبلغ المتبقي تلقائياً عند اختيار الفاتورة'),

                        DatePicker::make('payment_date')
                            ->label('تاريخ الدفع')
                            ->required()
                            ->default(now()),

                        Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options([
                                'cash' => 'نقداً',
                                'bank_transfer' => 'تحويل بنكي',
                                'credit_balance' => 'من رصيد المحفظة',
                            ])
                            ->default('cash')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->visible(fn (Get $get) => $get('user_id_selector') !== null),

                // قسم معلومات التحصيل
                Section::make('معلومات التحصيل')
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
                    ->columnSpan('full')
                    ->visible(fn (Get $get) => $get('user_id_selector') !== null),
            ]);
    }
}
