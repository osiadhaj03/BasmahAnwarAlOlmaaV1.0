<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\BadgeEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->description('المعلومات الأساسية للمعلم')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('الاسم الكامل')
                            ->icon('heroicon-o-user')
                            ->copyable(),

                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label('رقم الهاتف')
                            ->icon('heroicon-o-phone')
                            ->copyable(),

                        TextEntry::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->date('Y-m-d')
                            ->icon('heroicon-o-calendar')
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'غير محدد';
                                $age = now()->diffInYears($state);
                                return $state->format('Y-m-d') . " (العمر: {$age} سنة)";
                            }),
                    ]),

                Section::make('المعلومات المهنية')
                    ->description('المعلومات المتعلقة بالعمل والتخصص')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('employee_id')
                            ->label('رقم الموظف')
                            ->icon('heroicon-o-identification')
                            ->copyable(),

                        BadgeEntry::make('department')
                            ->label('القسم')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'computer_science' => 'علوم الحاسوب',
                                'mathematics' => 'الرياضيات',
                                'physics' => 'الفيزياء',
                                'chemistry' => 'الكيمياء',
                                'biology' => 'الأحياء',
                                'english' => 'اللغة الإنجليزية',
                                'arabic' => 'اللغة العربية',
                                'history' => 'التاريخ',
                                'geography' => 'الجغرافيا',
                                'islamic_studies' => 'الدراسات الإسلامية',
                                'other' => 'أخرى',
                                default => $state ?: 'غير محدد',
                            })
                            ->colors([
                                'primary' => 'computer_science',
                                'success' => 'mathematics',
                                'warning' => 'physics',
                                'danger' => 'chemistry',
                                'info' => 'biology',
                                'secondary' => ['english', 'arabic'],
                                'gray' => 'other',
                            ]),

                        TextEntry::make('specialization')
                            ->label('التخصص')
                            ->icon('heroicon-o-academic-cap')
                            ->placeholder('غير محدد'),

                        BadgeEntry::make('qualification')
                            ->label('المؤهل العلمي')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'bachelor' => 'بكالوريوس',
                                'master' => 'ماجستير',
                                'phd' => 'دكتوراه',
                                'diploma' => 'دبلوم',
                                'other' => 'أخرى',
                                default => $state ?: 'غير محدد',
                            })
                            ->colors([
                                'success' => 'phd',
                                'warning' => 'master',
                                'primary' => 'bachelor',
                                'secondary' => 'diploma',
                                'gray' => 'other',
                            ]),

                        TextEntry::make('hire_date')
                            ->label('تاريخ التوظيف')
                            ->date('Y-m-d')
                            ->icon('heroicon-o-calendar')
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'غير محدد';
                                $years = now()->diffInYears($state);
                                return $state->format('Y-m-d') . " (منذ {$years} سنة)";
                            }),

                        BadgeEntry::make('employment_status')
                            ->label('حالة التوظيف')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                'on_leave' => 'في إجازة',
                                'terminated' => 'منتهي الخدمة',
                                default => $state ?: 'غير محدد',
                            })
                            ->colors([
                                'success' => 'active',
                                'warning' => 'on_leave',
                                'danger' => ['inactive', 'terminated'],
                                'gray' => fn ($state) => !$state,
                            ])
                            ->icons([
                                'heroicon-o-check-circle' => 'active',
                                'heroicon-o-clock' => 'on_leave',
                                'heroicon-o-x-circle' => ['inactive', 'terminated'],
                            ]),
                    ]),

                Section::make('معلومات الاتصال')
                    ->description('معلومات العنوان والاتصال')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('address')
                            ->label('العنوان')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->placeholder('غير محدد'),

                        TextEntry::make('emergency_contact_name')
                            ->label('اسم جهة الاتصال في الطوارئ')
                            ->icon('heroicon-o-user')
                            ->placeholder('غير محدد'),

                        TextEntry::make('emergency_contact_phone')
                            ->label('رقم هاتف الطوارئ')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->placeholder('غير محدد'),

                        BadgeEntry::make('emergency_contact_relationship')
                            ->label('صلة القرابة')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'spouse' => 'الزوج/الزوجة',
                                'parent' => 'الوالد/الوالدة',
                                'sibling' => 'الأخ/الأخت',
                                'child' => 'الابن/الابنة',
                                'friend' => 'صديق',
                                'other' => 'أخرى',
                                default => $state ?: 'غير محدد',
                            })
                            ->colors([
                                'primary' => ['spouse', 'parent'],
                                'success' => 'sibling',
                                'warning' => 'child',
                                'info' => 'friend',
                                'gray' => 'other',
                            ]),
                    ]),

                Section::make('إحصائيات التدريس')
                    ->description('إحصائيات متعلقة بالدروس والطلاب')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('lessons_count')
                            ->label('عدد الدروس')
                            ->icon('heroicon-o-book-open')
                            ->formatStateUsing(fn ($record) => $record->lessons()->count() . ' درس')
                            ->color('primary'),

                        TextEntry::make('active_lessons_count')
                            ->label('الدروس النشطة')
                            ->icon('heroicon-o-play')
                            ->formatStateUsing(fn ($record) => $record->lessons()->where('status', 'active')->count() . ' درس')
                            ->color('success'),

                        TextEntry::make('total_students')
                            ->label('إجمالي الطلاب')
                            ->icon('heroicon-o-users')
                            ->formatStateUsing(function ($record) {
                                $totalStudents = $record->lessons()
                                    ->withCount('students')
                                    ->get()
                                    ->sum('students_count');
                                return $totalStudents . ' طالب';
                            })
                            ->color('info'),
                    ]),

                Section::make('ملاحظات إضافية')
                    ->description('أي ملاحظات أو معلومات إضافية')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(fn ($record) => !$record->notes)
                    ->schema([
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات')
                            ->formatStateUsing(fn ($state) => $state ?: 'لا توجد ملاحظات'),
                    ]),

                Section::make('معلومات النظام')
                    ->description('معلومات تقنية حول السجل')
                    ->icon('heroicon-o-cog')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-plus-circle'),

                        TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-pencil-square'),
                    ]),
            ]);
    }
}
