<?php

namespace App\Filament\Resources\KitchenInvoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KitchenInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('المشترك')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subscription.kitchen.name')
                    ->label('المطبخ')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('المبلغ المطلوب')
                    ->money('JOD')
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->label('المدفوع')
                    ->money('JOD')
                    ->color('success'),
                TextColumn::make('remaining_amount')
                    ->label('المتبقي')
                    ->money('JOD')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('billing_date')
                    ->label('تاريخ الفوترة')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'قيد الانتظار',
                        'paid' => 'مدفوعة',
                        'partial' => 'مدفوعة جزئياً',
                        'overdue' => 'متأخرة',
                        'cancelled' => 'ملغاة',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partial' => 'info',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'paid' => 'مدفوعة',
                        'partial' => 'مدفوعة جزئياً',
                        'overdue' => 'متأخرة',
                        'cancelled' => 'ملغاة',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record, DeleteAction $action) {
                        if ($record->allocations()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('⚠️ لا يمكن حذف الفاتورة')
                                ->body('هذه الفاتورة مرتبطة بدفعات. يرجى حذف الدفعات أولاً قبل حذف الفاتورة.')
                                ->danger()
                                ->persistent()
                                ->send();
                            
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records, DeleteBulkAction $action) {
                            $hasPayments = $records->filter(fn ($record) => $record->allocations()->count() > 0);
                            
                            if ($hasPayments->isNotEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('⚠️ لا يمكن حذف بعض الفواتير')
                                    ->body('الفواتير التالية مرتبطة بدفعات ولا يمكن حذفها: ' . $hasPayments->pluck('invoice_number')->join(', '))
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->defaultSort('billing_date', 'desc');
    }
}
