<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الحضور الأساسية')
                    ->schema([
                        TextEntry::make('lesson.title')
                            ->label('الدرس')
                            ->weight('bold')
                            ->color('primary'),
                        
                        TextEntry::make('student.name')
                            ->label('الطالب')
                            ->weight('bold'),
                        
                        TextEntry::make('status')
                            ->label('حالة الحضور')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'present' => 'حاضر',
                                'absent' => 'غائب',
                                'late' => 'متأخر',
                                'excused' => 'معذور',
                                default => $state,
                            })
                            ->colors([
                                'success' => 'present',
                                'danger' => 'absent',
                                'warning' => 'late',
                                'info' => 'excused',
                            ]),
                        
                        TextEntry::make('attendance_date')
                            ->label('تاريخ ووقت الحضور')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-calendar'),
                    ])
                    ->columns(2),
                
                Section::make('تفاصيل طريقة التسجيل')
                    ->schema([
                        TextEntry::make('attendance_method')
                            ->label('طريقة التسجيل')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'code' => 'بالكود',
                                'manual' => 'يدوي',
                                'auto' => 'تلقائي',
                                default => $state,
                            })
                            ->colors([
                                'primary' => 'code',
                                'secondary' => 'manual',
                                'success' => 'auto',
                            ]),
                        
                        TextEntry::make('used_code')
                            ->label('الكود المستخدم')
                            ->placeholder('لا يوجد')
                            ->copyable()
                            ->icon('heroicon-o-key'),
                        
                        TextEntry::make('marked_at')
                            ->label('وقت التسجيل')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('غير محدد')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('markedBy.name')
                            ->label('سجل بواسطة')
                            ->placeholder('النظام')
                            ->icon('heroicon-o-user'),
                    ])
                    ->columns(2),
                
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
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-plus-circle'),
                        
                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-pencil-square'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
