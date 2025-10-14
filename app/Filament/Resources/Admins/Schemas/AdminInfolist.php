<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AdminInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الشخصية')
                    ->description('المعلومات الأساسية للإداري')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('الاسم الكامل')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label('رقم الهاتف')
                            ->icon('heroicon-o-phone')
                            ->placeholder('غير محدد')
                            ->copyable(),

                        TextEntry::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->date('Y-m-d')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-cake'),

                        TextEntry::make('age')
                            ->label('العمر')
                            ->state(function ($record) {
                                if (!$record->date_of_birth) {
                                    return 'غير محدد';
                                }
                                return now()->diffInYears($record->date_of_birth) . ' سنة';
                            })
                            ->icon('heroicon-o-calendar-days'),
                    ]),

                Section::make('المعلومات الإدارية')
                    ->description('معلومات الوظيفة والصلاحيات')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('employee_id')
                            ->label('رقم الموظف')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-identification')
                            ->copyable(),

                        TextEntry::make('admin_level')
                            ->label('مستوى الإدارة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'super_admin' => 'danger',
                                'admin' => 'warning',
                                'moderator' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'super_admin' => 'مدير عام',
                                'admin' => 'إداري',
                                'moderator' => 'مشرف',
                                default => $state ?? 'غير محدد',
                            }),

                        TextEntry::make('department')
                            ->label('القسم')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'academic' => 'success',
                                'student_affairs' => 'info',
                                'hr' => 'warning',
                                'finance' => 'danger',
                                'it' => 'primary',
                                'general' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'academic' => 'الشؤون الأكاديمية',
                                'student_affairs' => 'شؤون الطلاب',
                                'hr' => 'الموارد البشرية',
                                'finance' => 'الشؤون المالية',
                                'it' => 'تقنية المعلومات',
                                'general' => 'الإدارة العامة',
                                default => 'غير محدد',
                            })
                            ->placeholder('غير محدد'),

                        IconEntry::make('is_active')
                            ->label('حالة الحساب')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        TextEntry::make('hire_date')
                            ->label('تاريخ التوظيف')
                            ->date('Y-m-d')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('years_of_service')
                            ->label('سنوات الخدمة')
                            ->state(function ($record) {
                                if (!$record->hire_date) {
                                    return 'غير محدد';
                                }
                                $years = now()->diffInYears($record->hire_date);
                                $months = now()->diffInMonths($record->hire_date) % 12;
                                return $years . ' سنة و ' . $months . ' شهر';
                            })
                            ->icon('heroicon-o-clock'),
                    ]),

                Section::make('معلومات الاتصال')
                    ->description('معلومات الاتصال والعنوان')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('address')
                            ->label('العنوان')
                            ->placeholder('غير محدد')
                            ->columnSpanFull()
                            ->icon('heroicon-o-map-pin'),

                        TextEntry::make('emergency_contact_name')
                            ->label('اسم جهة الاتصال في الطوارئ')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('emergency_contact_phone')
                            ->label('هاتف جهة الاتصال في الطوارئ')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-phone')
                            ->copyable(),

                        TextEntry::make('emergency_contact_relationship')
                            ->label('صلة القرابة')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'parent' => 'والد/والدة',
                                'spouse' => 'زوج/زوجة',
                                'sibling' => 'أخ/أخت',
                                'child' => 'ابن/ابنة',
                                'relative' => 'قريب',
                                'friend' => 'صديق',
                                'other' => 'أخرى',
                                default => $state ?? 'غير محدد',
                            })
                            ->placeholder('غير محدد'),
                    ]),

                Section::make('ملاحظات إضافية')
                    ->description('ملاحظات ومعلومات إضافية')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث السجل')
                    ->icon('heroicon-o-cog-6-tooth')
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

                        TextEntry::make('deleted_at')
                            ->label('تاريخ الحذف')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('غير محذوف')
                            ->icon('heroicon-o-trash')
                            ->visible(fn ($record) => $record->deleted_at !== null),
                    ]),
            ]);
    }
}
