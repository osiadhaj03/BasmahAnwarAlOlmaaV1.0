<?php

namespace App\Filament\Pages;

use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class AutoInvoiceCalculator extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'حساب الفواتير التلقائي';
    protected static ?string $title = 'حساب الفواتير التلقائي';
    protected static ?string $slug = 'auto-invoice-calculator';
    protected static UnitEnum|string|null $navigationGroup = 'إدارة الاشتراكات و الدفعات';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.auto-invoice-calculator';

    public ?string $fromDate = null;
    public ?string $toDate = null;

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('تحديد الفترة')
                    ->description('اختر الفترة لحساب الفواتير')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('fromDate')
                            ->label('من تاريخ')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('toDate')
                            ->label('إلى تاريخ')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->default(now()),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('subscriber_type')
                    ->label('النوع')
                    ->getStateUsing(fn ($record) => $this->getSubscriberType($record))
                    ->badge()
                    ->color(fn ($state) => str_contains($state, 'طالب') ? 'info' : 'success'),

                TextColumn::make('lectures_count')
                    ->label('المحاضرات')
                    ->getStateUsing(fn ($record) => $this->getLecturesCount($record))
                    ->badge()
                    ->color('warning'),

                TextColumn::make('attendance_count')
                    ->label('الحضور')
                    ->getStateUsing(fn ($record) => $this->getAttendanceCount($record))
                    ->badge()
                    ->color('success'),

                TextColumn::make('absence_count')
                    ->label('الغيابات')
                    ->getStateUsing(fn ($record) => $this->getAbsenceCount($record))
                    ->badge()
                    ->color('danger'),

                TextColumn::make('invoice_amount')
                    ->label('المبلغ')
                    ->getStateUsing(fn ($record) => $this->getInvoiceAmount($record))
                    ->money('ILS')
                    ->color(fn ($record) => $this->isFullPrice($record) ? 'danger' : 'primary')
                    ->weight('bold'),

                IconColumn::make('is_full_price')
                    ->label('كامل')
                    ->getStateUsing(fn ($record) => $this->isFullPrice($record))
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->bulkActions([
                BulkAction::make('generateInvoices')
                    ->label('✅ إنشاء فواتير للمحددين')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد إنشاء الفواتير')
                    ->modalDescription(fn (Collection $records) => "سيتم إنشاء {$records->count()} فاتورة للفترة من {$this->fromDate} إلى {$this->toDate}. هل أنت متأكد؟")
                    ->action(function (Collection $records) {
                        $this->generateInvoicesForUsers($records);
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->striped()
            ->defaultSort('name');
    }

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->whereHas('kitchenSubscriptions', fn ($q) => $q->where('status', 'active'))
            ->with(['kitchenSubscriptions' => fn ($q) => $q->where('status', 'active'), 'roles']);
    }

    protected function getSubscriberType($record): string
    {
        $isStudent = $record->hasRole('student');
        $isCustomer = $record->hasRole('customer');

        return match (true) {
            $isStudent && $isCustomer => 'طالب + زبون',
            $isStudent => 'طالب',
            $isCustomer => 'زبون',
            default => 'مشترك',
        };
    }

    protected function getLecturesCount($record): string
    {
        if (!$record->hasRole('student')) return '-';

        $data = $record->calculateAbsencePenaltyForPeriod($this->fromDate, $this->toDate);
        return $data['total_lectures'] > 0 ? (string) $data['total_lectures'] : '-';
    }

    protected function getAttendanceCount($record): string
    {
        if (!$record->hasRole('student')) return '-';

        $data = $record->calculateAbsencePenaltyForPeriod($this->fromDate, $this->toDate);
        return $data['total_lectures'] > 0 ? (string) $data['attended_lectures'] : '-';
    }

    protected function getAbsenceCount($record): string
    {
        if (!$record->hasRole('student')) return '-';

        $data = $record->calculateAbsencePenaltyForPeriod($this->fromDate, $this->toDate);
        return $data['total_lectures'] > 0 ? (string) $data['absence_count'] : '-';
    }

    protected function getInvoiceAmount($record): float
    {
        $subscription = $record->kitchenSubscriptions->first();
        if (!$subscription) return 0;

        if (!$record->hasRole('student')) {
            return (float) $subscription->monthly_price;
        }

        $data = $record->calculateAbsencePenaltyForPeriod($this->fromDate, $this->toDate);

        if ($data['total_lectures'] == 0 || $data['attended_lectures'] == 0) {
            return (float) $subscription->monthly_price;
        }

        return (float) $data['penalty_amount'];
    }

    protected function isFullPrice($record): bool
    {
        $subscription = $record->kitchenSubscriptions->first();
        if (!$subscription) return false;

        if (!$record->hasRole('student')) return true;

        $data = $record->calculateAbsencePenaltyForPeriod($this->fromDate, $this->toDate);
        return $data['total_lectures'] == 0 || $data['attended_lectures'] == 0;
    }

    protected function generateInvoicesForUsers(Collection $users): void
    {
        $createdCount = 0;
        $totalAmount = 0;

        foreach ($users as $user) {
            $subscription = $user->kitchenSubscriptions->first();
            if (!$subscription) continue;

            $amount = $this->getInvoiceAmount($user);

            KitchenInvoice::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'invoice_number' => KitchenInvoice::generateInvoiceNumber(),
                'amount' => $amount,
                'billing_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'pending',
            ]);

            $createdCount++;
            $totalAmount += $amount;
        }

        Notification::make()
            ->title('✅ تم إنشاء الفواتير')
            ->body("تم إنشاء {$createdCount} فاتورة بإجمالي " . number_format($totalAmount, 2) . " ₪")
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('تحديث الجدول')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->resetTable()),
        ];
    }
}
