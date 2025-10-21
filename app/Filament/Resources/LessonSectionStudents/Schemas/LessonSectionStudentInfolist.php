<?php

namespace App\Filament\Resources\LessonSectionStudents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;

class LessonSectionStudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الطالب')
                    ->description('تفاصيل الطالب المسجل')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('student.name')
                                    ->label('اسم الطالب')
                                    ->icon('heroicon-o-user'),
                                
                                TextEntry::make('student.student_id')
                                    ->label('رقم الطالب')
                                    ->icon('heroicon-o-identification'),
                                
                                TextEntry::make('student.email')
                                    ->label('البريد الإلكتروني')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                                
                                TextEntry::make('student.phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('غير محدد'),
                            ]),
                    ]),
                
                Section::make('معلومات الدورة')
                    ->description('تفاصيل قسم الدورة المسجل فيها')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('lessonSection.name')
                                    ->label('اسم قسم الدورة')
                                    ->icon('heroicon-o-book-open'),
                                
                                TextEntry::make('lessonSection.description')
                                    ->label('وصف القسم')
                                    ->placeholder('لا يوجد وصف')
                                    ->columnSpanFull(),
                                
                                TextEntry::make('lessonSection.lessons_count')
                                    ->label('عدد الدروس')
                                    ->icon('heroicon-o-list-bullet')
                                    ->getStateUsing(fn ($record) => $record->lessonSection->lessons()->count()),
                                
                                TextEntry::make('lessonSection.max_students')
                                    ->label('الحد الأقصى للطلاب')
                                    ->icon('heroicon-o-users')
                                    ->placeholder('غير محدد'),
                            ]),
                    ]),
                
                Section::make('معلومات التسجيل')
                    ->description('تفاصيل عملية التسجيل')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('enrolled_at')
                                    ->label('تاريخ التسجيل')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-calendar'),
                                
                                TextEntry::make('enrollment_status_arabic')
                                    ->label('حالة التسجيل')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'نشط' => 'success',
                                        'متوقف' => 'warning',
                                        'مكتمل' => 'gray',
                                        default => 'gray',
                                    }),
                                
                                TextEntry::make('notes')
                                    ->label('ملاحظات')
                                    ->placeholder('لا توجد ملاحظات')
                                    ->columnSpanFull()
                                    ->markdown(),
                            ]),
                    ]),
                
                Section::make('معلومات النظام')
                    ->description('تواريخ الإنشاء والتحديث')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-plus-circle'),
                                
                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-pencil-square'),
                            ]),
                    ]),
            ]);
    }
}
