<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\KitchenExpense;
use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\Lecture;
use App\Models\Lesson;
use App\Models\MealDelivery;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        // 1. إحصائيات المطبخ والوجبات (Kitchen & Meals)
        $todayMealsDelivered = MealDelivery::today()->delivered()->count();
        $kitchenSubscribers = KitchenSubscription::distinct('user_id')->count('user_id');
        
        // عدد الطباخين (Cooks) - based on role or type
        $cooksCount = User::where(function($q) {
            $q->where('type', 'cook')->orWhereHas('roles', function($q2) {
                $q2->where('slug', 'cook');
            });
        })->count();


        // 2. المحاسبة والمالية (Financials)
        $totalPaid = KitchenInvoice::paid()->sum('amount');
        $totalUnpaid = KitchenInvoice::unpaid()->sum('amount');
        $monthlyExpenses = KitchenExpense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');
        
        $subscribersBalance = KitchenSubscription::get()->sum(function ($subscription) {
            $balance = $subscription->balance;
            return $balance > 0 ? $balance : 0; 
        });


        // 3. التعليم (Education)
        $totalLessons = Lesson::count();
        $monthlyLectures = Lecture::whereMonth('lecture_date', $currentMonth)
            ->whereYear('lecture_date', $currentYear)
            ->count();
        $enrolledStudents = DB::table('lesson_student')->distinct('student_id')->count('student_id');
        $todayAttendance = Attendance::whereDate('attendance_date', $today)->where('status', 'present')->count();
        
        // عدد الأساتذة (Teachers) - based on role or type
        $teachersCount = User::where(function($q) {
            $q->where('type', 'teacher')->orWhereHas('roles', function($q2) {
                $q2->where('slug', 'teacher');
            });
        })->count();


        return [
            // --- صف المطبخ والوجبات ---
            Stat::make('الوجبات المسلمة اليوم', $todayMealsDelivered)
                ->description($today->format('Y/m/d'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('المشتركين (المطبخ)', $kitchenSubscribers)
                ->description('في اشتراكات المطبخ')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('عدد الطباخين', $cooksCount)
                ->description('الطاقم النشط')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            // --- صف المالية ---
            Stat::make('المبالغ المحصلة', number_format($totalPaid, 2) . ' د.أ')
                ->description('إجمالي الفواتير المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('المبالغ المتبقية', number_format($totalUnpaid, 2) . ' د.أ')
                ->description('الفواتير غير المدفوعة')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('مصاريف الشهر', number_format($monthlyExpenses, 2) . ' د.أ')
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning'),

            Stat::make('أرصدة المشتركين', number_format($subscribersBalance, 2) . ' د.أ')
                ->description('مبالغ زائدة لصالح المشتركين')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),

            // --- صف التعليم ---
            Stat::make('عدد الأساتذة', $teachersCount)
                ->description('الهيئة التدريسية')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('الطلاب المسجلين', $enrolledStudents)
                ->description('في الدورات التعليمية')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('عدد الدورات', $totalLessons)
                ->description('إجمالي الدورات')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),

            Stat::make('محاضرات الشهر', $monthlyLectures)
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-presentation-chart-bar')
                ->color('primary'),

            Stat::make('الحضور اليوم', $todayAttendance)
                ->description('طلاب حاضرين')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}
