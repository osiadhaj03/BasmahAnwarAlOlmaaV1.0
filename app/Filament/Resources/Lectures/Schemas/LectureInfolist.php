<?php

namespace App\Filament\Resources\Lectures\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LectureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات المحاضرة الأساسية')
                    ->description('المعلومات الأساسية للمحاضرة')
                    ->schema([
                        TextEntry::make('title')
                            ->label('عنوان المحاضرة'),

                        TextEntry::make('description')
                            ->label('وصف المحاضرة')
                            ->columnSpanFull(),

                        TextEntry::make('lesson.title')
                            ->label('الدورة'),

                        TextEntry::make('lecture_number')
                            ->label('رقم المحاضرة'),
                    ])
                    ->columns(2),

                Section::make('التوقيت والمكان')
                    ->description('معلومات التوقيت والحالة')
                    ->schema([
                        TextEntry::make('lecture_date')
                            ->label('تاريخ ووقت المحاضرة')
                            ->dateTime('Y-m-d H:i'),

                        TextEntry::make('duration_minutes')
                            ->label('مدة المحاضرة')
                            ->formatStateUsing(fn ($state) => $state . ' دقيقة'),

                        TextEntry::make('location')
                            ->label('مكان المحاضرة'),

                        TextEntry::make('status')
                            ->label('حالة المحاضرة')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'scheduled' => 'مجدولة',
                                'ongoing' => 'جارية',
                                'completed' => 'مكتملة',
                                'cancelled' => 'ملغية',
                                default => 'غير محدد',
                            })
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'scheduled' => 'warning',
                                'ongoing' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary',
                            }),
                    ])
                    ->columns(2),

                Section::make('إعدادات إضافية')
                    ->description('الإعدادات والملاحظات')
                    ->schema([
                        TextEntry::make('is_mandatory')
                            ->label('محاضرة إجبارية')
                            ->formatStateUsing(fn ($state) => $state ? 'نعم' : 'لا')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'secondary'),

                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),

                        TextEntry::make('recording_url')
                            ->label('رابط تسجيل المحاضرة')
                            ->url()
                            ->openUrlInNewTab()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('معلومات النظام')
                    ->description('معلومات الإنشاء والتحديث')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i'),

                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime('Y-m-d H:i'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
