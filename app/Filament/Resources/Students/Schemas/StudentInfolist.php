<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->schema([
                        TextEntry::make('name')
                            ->label('الاسم الكامل')
                            ->weight('bold')
                            ->color('primary')
                            ->icon('heroicon-o-user'),
                        
                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->copyable()
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-envelope'),
                        
                        TextEntry::make('phone')
                            ->label('رقم الهاتف')
                            ->copyable()
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-phone'),
                        
                        TextEntry::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->date('Y-m-d')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-calendar-days'),
                        
                        TextEntry::make('age')
                            ->label('العمر')
                            ->getStateUsing(function ($record) {
                                if (!$record->date_of_birth) return 'غير محدد';
                                return now()->diffInYears($record->date_of_birth) . ' سنة';
                            })
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
                
                Section::make('المعلومات الأكاديمية')
                    ->schema([
                        TextEntry::make('student_id')
                            ->label('الرقم الجامعي')
                            ->weight('bold')
                            ->copyable()
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-identification'),
                        
                        TextEntry::make('level')
                            ->label('المستوى الدراسي')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'freshman' => 'السنة الأولى',
                                'sophomore' => 'السنة الثانية',
                                'junior' => 'السنة الثالثة',
                                'senior' => 'السنة الرابعة',
                                'graduate' => 'دراسات عليا',
                                default => $state,
                            })
                            ->colors([
                                'primary' => 'freshman',
                                'success' => 'sophomore',
                                'warning' => 'junior',
                                'danger' => 'senior',
                                'info' => 'graduate',
                            ])
                            ->placeholder('غير محدد'),
                        
                        TextEntry::make('major')
                            ->label('التخصص')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-academic-cap'),
                        
                        TextEntry::make('status')
                            ->label('حالة الطالب')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'graduated' => 'متخرج',
                                'suspended' => 'موقوف',
                                'transferred' => 'محول',
                                default => $state,
                            })
                            ->colors([
                                'success' => 'active',
                                'gray' => 'inactive',
                                'info' => 'graduated',
                                'danger' => 'suspended',
                                'warning' => 'transferred',
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('معلومات الاتصال')
                    ->schema([
                        TextEntry::make('address')
                            ->label('العنوان')
                            ->placeholder('غير محدد')
                            ->columnSpanFull()
                            ->icon('heroicon-o-map-pin'),
                        
                        TextEntry::make('emergency_contact_name')
                            ->label('اسم جهة الاتصال في الطوارئ')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-user-plus'),
                        
                        TextEntry::make('emergency_contact_phone')
                            ->label('رقم هاتف الطوارئ')
                            ->copyable()
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-phone'),
                        
                        TextEntry::make('emergency_contact_relation')
                            ->label('صلة القرابة')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'father' => 'الأب',
                                'mother' => 'الأم',
                                'brother' => 'الأخ',
                                'sister' => 'الأخت',
                                'uncle' => 'العم',
                                'aunt' => 'العمة',
                                'guardian' => 'الوصي',
                                'other' => 'أخرى',
                                default => $state,
                            })
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-heart'),
                    ])
                    ->columns(2),
                
                Section::make('إحصائيات الحضور')
                    ->schema([
                        TextEntry::make('total_lessons')
                            ->label('إجمالي الدروس المسجلة')
                            ->getStateUsing(function ($record) {
                                return $record->attendances()->distinct('lesson_id')->count();
                            })
                            ->icon('heroicon-o-academic-cap'),
                        
                        TextEntry::make('present_count')
                            ->label('عدد مرات الحضور')
                            ->getStateUsing(function ($record) {
                                return $record->attendances()->where('status', 'present')->count();
                            })
                            ->color('success')
                            ->icon('heroicon-o-check-circle'),
                        
                        TextEntry::make('absent_count')
                            ->label('عدد مرات الغياب')
                            ->getStateUsing(function ($record) {
                                return $record->attendances()->where('status', 'absent')->count();
                            })
                            ->color('danger')
                            ->icon('heroicon-o-x-circle'),
                        
                        TextEntry::make('late_count')
                            ->label('عدد مرات التأخير')
                            ->getStateUsing(function ($record) {
                                return $record->attendances()->where('status', 'late')->count();
                            })
                            ->color('warning')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('attendance_rate')
                            ->label('معدل الحضور')
                            ->getStateUsing(function ($record) {
                                $total = $record->attendances()->count();
                                if ($total === 0) return 'لا توجد بيانات';
                                $present = $record->attendances()->whereIn('status', ['present', 'late'])->count();
                                return round(($present / $total) * 100, 1) . '%';
                            })
                            ->color(function ($record) {
                                $total = $record->attendances()->count();
                                if ($total === 0) return 'gray';
                                $present = $record->attendances()->whereIn('status', ['present', 'late'])->count();
                                $rate = ($present / $total) * 100;
                                if ($rate >= 90) return 'success';
                                if ($rate >= 75) return 'warning';
                                return 'danger';
                            })
                            ->icon('heroicon-o-chart-pie'),
                    ])
                    ->columns(3),
                
                Section::make('ملاحظات إضافية')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('الملاحظات')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => empty($record->notes)),
                
                Section::make('معلومات النظام')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ التسجيل')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-plus-circle'),
                        
                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-pencil-square'),
                        
                        TextEntry::make('deleted_at')
                            ->label('تاريخ الحذف')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('غير محذوف')
                            ->icon('heroicon-o-trash'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
