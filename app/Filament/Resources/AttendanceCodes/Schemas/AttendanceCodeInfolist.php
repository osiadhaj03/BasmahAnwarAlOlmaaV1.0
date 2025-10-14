<?php

namespace App\Filament\Resources\AttendanceCodes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceCodeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الكود الأساسية')
                    ->schema([
                        TextEntry::make('lesson.title')
                            ->label('الدرس')
                            ->weight('bold')
                            ->color('primary')
                            ->icon('heroicon-o-academic-cap'),
                        
                        TextEntry::make('code')
                            ->label('الكود')
                            ->weight('bold')
                            ->color('success')
                            ->copyable()
                            ->icon('heroicon-o-qr-code'),
                        
                        IconEntry::make('is_active')
                            ->label('نشط')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->getStateUsing(function ($record) {
                                if (!$record->is_active) return 'غير نشط';
                                if ($record->expires_at < now()) return 'منتهي الصلاحية';
                                if ($record->max_usage && $record->usage_count >= $record->max_usage) return 'مستنفد';
                                return 'نشط';
                            })
                            ->colors([
                                'success' => 'نشط',
                                'danger' => ['غير نشط', 'منتهي الصلاحية', 'مستنفد'],
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('معلومات التوقيت والصلاحية')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-plus-circle'),
                        
                        TextEntry::make('expires_at')
                            ->label('تاريخ انتهاء الصلاحية')
                            ->dateTime('Y-m-d H:i:s')
                            ->color(fn ($record) => $record->expires_at < now() ? 'danger' : 'success')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('deactivated_at')
                            ->label('تاريخ إلغاء التفعيل')
                            ->dateTime('Y-m-d H:i:s')
                            ->placeholder('لم يتم إلغاء التفعيل')
                            ->icon('heroicon-o-x-circle'),
                        
                        TextEntry::make('time_remaining')
                            ->label('الوقت المتبقي')
                            ->getStateUsing(function ($record) {
                                if ($record->expires_at < now()) {
                                    return 'منتهي الصلاحية';
                                }
                                $diff = now()->diff($record->expires_at);
                                if ($diff->days > 0) {
                                    return $diff->days . ' يوم';
                                } elseif ($diff->h > 0) {
                                    return $diff->h . ' ساعة';
                                } else {
                                    return $diff->i . ' دقيقة';
                                }
                            })
                            ->color(fn ($record) => $record->expires_at < now() ? 'danger' : 'success')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
                
                Section::make('إحصائيات الاستخدام')
                    ->schema([
                        TextEntry::make('usage_count')
                            ->label('عدد مرات الاستخدام')
                            ->numeric()
                            ->icon('heroicon-o-chart-bar'),
                        
                        TextEntry::make('max_usage')
                            ->label('الحد الأقصى للاستخدام')
                            ->placeholder('غير محدود')
                            ->icon('heroicon-o-stop'),
                        
                        TextEntry::make('usage_percentage')
                            ->label('نسبة الاستخدام')
                            ->getStateUsing(function ($record) {
                                if (!$record->max_usage) return 'غير محدود';
                                $percentage = ($record->usage_count / $record->max_usage) * 100;
                                return round($percentage, 1) . '%';
                            })
                            ->color(function ($record) {
                                if (!$record->max_usage) return 'gray';
                                $percentage = ($record->usage_count / $record->max_usage) * 100;
                                if ($percentage >= 90) return 'danger';
                                if ($percentage >= 70) return 'warning';
                                return 'success';
                            })
                            ->icon('heroicon-o-chart-pie'),
                        
                        TextEntry::make('remaining_usage')
                            ->label('الاستخدامات المتبقية')
                            ->getStateUsing(function ($record) {
                                if (!$record->max_usage) return 'غير محدود';
                                return max(0, $record->max_usage - $record->usage_count);
                            })
                            ->icon('heroicon-o-minus-circle'),
                    ])
                    ->columns(2),
                
                Section::make('معلومات المستخدمين')
                    ->schema([
                        TextEntry::make('createdBy.name')
                            ->label('أنشأ بواسطة')
                            ->placeholder('النظام')
                            ->icon('heroicon-o-user-plus'),
                        
                        TextEntry::make('deactivatedBy.name')
                            ->label('ألغى التفعيل')
                            ->placeholder('لم يتم إلغاء التفعيل')
                            ->icon('heroicon-o-user-minus'),
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
                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime('Y-m-d H:i:s')
                            ->icon('heroicon-o-pencil-square'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
