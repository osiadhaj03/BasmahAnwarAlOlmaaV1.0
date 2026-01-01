<?php

namespace App\Livewire;

use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\User;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AutoInvoiceCalculator extends Component
{
    public bool $showModal = false;
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public array $subscribers = [];
    public array $selectedSubscribers = [];
    public bool $calculated = false;
    public float $totalAmount = 0;

    public function mount()
    {
        // تعيين الفترة الافتراضية: أول الشهر الحالي إلى اليوم
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->calculated = false;
        $this->subscribers = [];
        $this->selectedSubscribers = [];
        $this->totalAmount = 0;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['subscribers', 'selectedSubscribers', 'calculated', 'totalAmount']);
    }

    public function calculate()
    {
        $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
        ]);

        // جلب جميع المشتركين الذين لديهم اشتراكات فعالة
        $activeSubscriptions = KitchenSubscription::where('status', 'active')
            ->with('user.roles')
            ->get();

        $this->subscribers = [];
        
        foreach ($activeSubscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user) continue;

            // تحديد نوع المشترك
            $isStudent = $user->hasRole('student');
            $isCustomer = $user->hasRole('customer');
            
            $subscriberType = match(true) {
                $isStudent && $isCustomer => 'طالب + زبون',
                $isStudent => 'طالب',
                $isCustomer => 'زبون',
                default => 'مشترك',
            };

            // حساب الغياب والمبلغ
            if ($isStudent) {
                $penaltyData = $user->calculateAbsencePenaltyForPeriod($this->fromDate, $this->toDate);
                
                // إذا لا توجد محاضرات أو لا يوجد حضور = الاشتراك الكامل
                if ($penaltyData['total_lectures'] == 0 || $penaltyData['attended_lectures'] == 0) {
                    $amount = (float) $subscription->monthly_price;
                    $absenceCount = '-';
                    $attendanceCount = '-';
                    $lecturesCount = '-';
                    $absencePrice = '-';
                    $isFullPrice = true;
                } else {
                    $amount = $penaltyData['penalty_amount'];
                    $absenceCount = $penaltyData['absence_count'];
                    $attendanceCount = $penaltyData['attended_lectures'];
                    $lecturesCount = $penaltyData['total_lectures'];
                    $absencePrice = $penaltyData['absence_price'];
                    $isFullPrice = false;
                }
            } else {
                // زبون فقط = الاشتراك الكامل
                $amount = (float) $subscription->monthly_price;
                $absenceCount = '-';
                $attendanceCount = '-';
                $lecturesCount = '-';
                $absencePrice = '-';
                $isFullPrice = true;
            }

            $this->subscribers[] = [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'name' => $user->name,
                'type' => $subscriberType,
                'lectures_count' => $lecturesCount,
                'attendance_count' => $attendanceCount,
                'absence_count' => $absenceCount,
                'absence_price' => $absencePrice,
                'amount' => $amount,
                'monthly_price' => (float) $subscription->monthly_price,
                'is_full_price' => $isFullPrice,
            ];
        }

        // تحديد الكل افتراضياً
        $this->selectedSubscribers = array_column($this->subscribers, 'user_id');
        $this->calculateTotal();
        $this->calculated = true;
    }

    public function toggleSubscriber($userId)
    {
        if (in_array($userId, $this->selectedSubscribers)) {
            $this->selectedSubscribers = array_filter($this->selectedSubscribers, fn($id) => $id != $userId);
        } else {
            $this->selectedSubscribers[] = $userId;
        }
        $this->calculateTotal();
    }

    public function selectAll()
    {
        $this->selectedSubscribers = array_column($this->subscribers, 'user_id');
        $this->calculateTotal();
    }

    public function deselectAll()
    {
        $this->selectedSubscribers = [];
        $this->totalAmount = 0;
    }

    protected function calculateTotal()
    {
        $this->totalAmount = 0;
        foreach ($this->subscribers as $subscriber) {
            if (in_array($subscriber['user_id'], $this->selectedSubscribers)) {
                $this->totalAmount += $subscriber['amount'];
            }
        }
    }

    public function generateInvoices()
    {
        if (empty($this->selectedSubscribers)) {
            Notification::make()
                ->title('خطأ')
                ->body('يرجى اختيار مشترك واحد على الأقل')
                ->danger()
                ->send();
            return;
        }

        $createdCount = 0;
        $totalAmount = 0;

        foreach ($this->subscribers as $subscriber) {
            if (!in_array($subscriber['user_id'], $this->selectedSubscribers)) {
                continue;
            }

            // إنشاء الفاتورة
            KitchenInvoice::create([
                'subscription_id' => $subscriber['subscription_id'],
                'user_id' => $subscriber['user_id'],
                'invoice_number' => KitchenInvoice::generateInvoiceNumber(),
                'amount' => $subscriber['amount'],
                'billing_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'pending',
            ]);

            $createdCount++;
            $totalAmount += $subscriber['amount'];
        }

        Notification::make()
            ->title('✅ تم إنشاء الفواتير')
            ->body("تم إنشاء {$createdCount} فاتورة بإجمالي " . number_format($totalAmount, 2) . " ₪")
            ->success()
            ->send();

        $this->closeModal();
        
        // إعادة تحميل الصفحة
        $this->dispatch('refresh');
    }

    public function render()
    {
        return view('livewire.auto-invoice-calculator');
    }
}
