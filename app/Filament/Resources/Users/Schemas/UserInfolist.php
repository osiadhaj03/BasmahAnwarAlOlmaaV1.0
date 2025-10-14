<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        TextEntry::make('name')
                            ->label('الاسم الكامل'),
                        
                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->copyable(),
                        
                        TextEntry::make('phone')
                            ->label('رقم الهاتف')
                            ->placeholder('غير محدد')
                            ->copyable(),
                        
                        TextEntry::make('type')
                            ->label('نوع المستخدم')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'danger',
                                'teacher' => 'warning',
                                'student' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'admin' => 'مدير',
                                'teacher' => 'معلم',
                                'student' => 'طالب',
                                default => $state,
                            }),
                        
                        TextEntry::make('gender')
                            ->label('الجنس')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                                default => 'غير محدد',
                            })
                            ->placeholder('غير محدد'),
                        
                        TextEntry::make('birth_date')
                            ->label('تاريخ الميلاد')
                            ->date()
                            ->placeholder('غير محدد'),
                        
                        IconEntry::make('is_active')
                            ->label('الحالة')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])->columns(2),
                
                Section::make('معلومات إضافية')
                    ->schema([
                        TextEntry::make('student_id')
                            ->label('رقم الطالب')
                            ->placeholder('غير محدد')
                            ->visible(fn ($record) => $record->type === 'student'),
                        
                        TextEntry::make('employee_id')
                            ->label('رقم الموظف')
                            ->placeholder('غير محدد')
                            ->visible(fn ($record) => in_array($record->type, ['admin', 'teacher'])),
                        
                        TextEntry::make('department')
                            ->label('القسم')
                            ->placeholder('غير محدد'),
                        
                        TextEntry::make('bio')
                            ->label('نبذة شخصية')
                            ->placeholder('لا توجد نبذة')
                            ->columnSpanFull(),
                        
                        TextEntry::make('address')
                            ->label('العنوان')
                            ->placeholder('غير محدد')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Section::make('معلومات النظام')
                    ->schema([
                        TextEntry::make('email_verified_at')
                            ->label('تاريخ تأكيد البريد الإلكتروني')
                            ->dateTime()
                            ->placeholder('لم يتم التأكيد'),
                        
                        TextEntry::make('last_login_at')
                            ->label('آخر تسجيل دخول')
                            ->dateTime()
                            ->placeholder('لم يسجل دخول من قبل'),
                        
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),
                        
                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime(),
                    ])->columns(2),
                
                Section::make('الإحصائيات')
                    ->schema([
                        TextEntry::make('teacherLessons')
                            ->label('عدد الدروس المُدرَّسة')
                            ->state(fn ($record) => $record->teacherLessons()->count())
                            ->visible(fn ($record) => $record->type === 'teacher'),
                        
                        TextEntry::make('studentLessons')
                            ->label('عدد الدروس المسجل بها')
                            ->state(fn ($record) => $record->studentLessons()->count())
                            ->visible(fn ($record) => $record->type === 'student'),
                        
                        TextEntry::make('attendances')
                            ->label('عدد سجلات الحضور')
                            ->state(fn ($record) => $record->attendances()->count())
                            ->visible(fn ($record) => $record->type === 'student'),
                        
                        TextEntry::make('createdAttendanceCodes')
                            ->label('عدد أكواد الحضور المُنشأة')
                            ->state(fn ($record) => $record->createdAttendanceCodes()->count())
                            ->visible(fn ($record) => in_array($record->type, ['admin', 'teacher'])),
                    ])->columns(2),
            ]);
    }
}
