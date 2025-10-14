<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LessonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الدرس الأساسية')
                    ->description('المعلومات الأساسية للدرس')
                    ->schema([
                        TextEntry::make('title')
                            ->label('عنوان الدرس')
                            ->weight('bold'),
                        
                        TextEntry::make('description')
                            ->label('وصف الدرس')
                            ->placeholder('لا يوجد وصف')
                            ->columnSpanFull(),
                        
                        TextEntry::make('teacher.name')
                            ->label('المعلم')
                            ->weight('bold'),
                        
                        TextEntry::make('location')
                            ->label('مكان الدرس')
                            ->placeholder('غير محدد'),
                    ])->columns(2),
                
                Section::make('التوقيت والجدولة')
                    ->description('معلومات التوقيت والحالة')
                    ->schema([
                        TextEntry::make('lesson_date')
                            ->label('تاريخ الدرس')
                            ->date('Y-m-d'),
                        
                        TextEntry::make('start_time')
                            ->label('وقت البداية')
                            ->time('H:i'),
                        
                        TextEntry::make('end_time')
                            ->label('وقت النهاية')
                            ->time('H:i'),
                        
                        TextEntry::make('status')
                            ->label('حالة الدرس')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط',
                                'cancelled' => 'ملغي',
                                'completed' => 'مكتمل',
                                default => $state,
                            })
                            ->colors([
                                'success' => 'active',
                                'danger' => 'cancelled',
                                'primary' => 'completed',
                            ]),
                    ])->columns(2),
                
                Section::make('إعدادات إضافية')
                    ->description('الإعدادات والملاحظات')
                    ->schema([
                        TextEntry::make('max_students')
                            ->label('الحد الأقصى للطلاب')
                            ->numeric()
                            ->placeholder('غير محدود'),
                        
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull(),
                    ])->columns(1),
                
                Section::make('الإحصائيات')
                    ->description('إحصائيات الدرس')
                    ->schema([
                        TextEntry::make('attendances_count')
                            ->label('عدد الحضور')
                            ->getStateUsing(fn ($record) => $record->attendances()->count()),
                        
                        TextEntry::make('attendance_codes_count')
                            ->label('أكواد الحضور')
                            ->getStateUsing(fn ($record) => $record->attendanceCodes()->count()),
                    ])->columns(2),
                
                Section::make('معلومات النظام')
                    ->description('معلومات الإنشاء والتحديث')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i'),
                        
                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime('Y-m-d H:i'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
