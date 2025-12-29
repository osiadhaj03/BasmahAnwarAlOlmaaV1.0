<?php

namespace App\Filament\Resources\Subscribers\Tables;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Carbon\Carbon;

class SubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),
                
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->placeholder('غير محدد')
                    ->icon('heroicon-o-phone'),
                               
                TextColumn::make('kitchenSubscriptions.subscription_number')
                    ->label('رقم الاشتراك')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->placeholder('-'),
                
                TextColumn::make('kitchenSubscriptions.status')
                    ->label('حالة الاشتراك')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'فعال',
                        'expired' => 'منتهي',
                        'cancelled' => 'ملغي',
                        'pending' => 'قيد الانتظار',
                        default => $state ?? '-',
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

            
                
                TextColumn::make('outstanding_balance')
                    ->label('المبالغ المستحقة')
                    ->getStateUsing(function ($record) {
                        $subscription = $record->kitchenSubscriptions()->where('status', 'active')->first();
                        if (!$subscription) return 0;
                        
                        $totalInvoices = $subscription->invoices()->sum('amount');
                        $totalPaid = $subscription->invoices->sum(fn ($inv) => $inv->total_paid);
                        return $totalInvoices - $totalPaid;
                    })
                    ->money('jod')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->label('حالة الاشتراك')
                    ->options([
                        'active' => 'فعال',
                        'expired' => 'منتهي',
                        'cancelled' => 'ملغي',
                        'pending' => 'قيد الانتظار',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('kitchenSubscriptions', fn ($q) => $q->where('status', $data['value']));
                        }
                    }),
                
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الجميع')
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط'),
            ])
            ->recordActions([
//
            ])
            ->headerActions([
            //
            ])
            ->bulkActions([
                
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
