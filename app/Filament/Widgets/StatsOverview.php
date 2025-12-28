<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\KitchenExpense;
use App\Models\KitchenInvoice;
use App\Models\KitchenSubscription;
use App\Models\Lecture;
use App\Models\Lesson;
use App\Models\MealDelivery;
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

        // الوجبات المسلمة اليوم
        $todayMealsDelivered = MealDelivery::today()->delivered()->count();

        // المبالغ المحصلة والمتبقية
        $totalPaid = KitchenInvoice::paid()->sum('amount');
        $totalUnpaid = KitchenInvoice::unpaid()->sum('amount');

        // عدد الطلاب المسجلين في الدورات
        $enrolledStudents = DB::table('lesson_student')->distinct('student_id')->count('student_id');

        // عدد المشتركين في المطبخ
        $kitchenSubscribers = KitchenSubscription::distinct('user_id')->count('user_id');

        // الحضور اليوم
        $todayAttendance = Attendance::whereDate('attendance_date', $today)->where('status', 'present')->count();

        // عدد الدورات
        $totalLessons = Lesson::count();

        // مصاريف هذا الشهر
        $monthlyExpenses = KitchenExpense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        // عدد المحاضرات هذا الشهر
        $monthlyLectures = Lecture::whereMonth('lecture_date', $currentMonth)
            ->whereYear('lecture_date', $currentYear)
            ->count();

        // أرصدة المشتركين (الدفعات الزائدة عن الفواتير)
        $subscribersBalance = KitchenSubscription::get()->sum(function ($subscription) {
            $balance = $subscription->balance;
            return $balance > 0 ? $balance : 0; // فقط الأرصدة الموجبة
        });

        return [
            Stat::make('الوجبات المسلمة اليوم', $todayMealsDelivered)
                ->description($today->format('Y/m/d'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('المبالغ المحصلة', number_format($totalPaid, 2) . ' د.أ')
                ->description('إجمالي الفواتير المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('المبالغ المتبقية', number_format($totalUnpaid, 2) . ' د.أ')
                ->description('الفواتير غير المدفوعة')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('الطلاب المسجلين', $enrolledStudents)
                ->description('في الدورات التعليمية')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('المشتركين (المطبخ)', $kitchenSubscribers)
                ->description('في اشتراكات المطبخ')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('الحضور اليوم', $todayAttendance)
                ->description('طلاب حاضرين')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('عدد الدورات', $totalLessons)
                ->description('إجمالي الدورات')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),

            Stat::make('مصاريف الشهر', number_format($monthlyExpenses, 2) . ' د.أ')
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning'),

            Stat::make('محاضرات الشهر', $monthlyLectures)
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-presentation-chart-bar')
                ->color('primary'),

            Stat::make('أرصدة المشتركين', number_format($subscribersBalance, 2) . ' د.أ')
                ->description('مبالغ زائدة لصالح المشتركين')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),
        ];
    }
}
