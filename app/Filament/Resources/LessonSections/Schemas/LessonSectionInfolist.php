<?php

namespace App\Filament\Resources\LessonSections\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LessonSectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات القسم')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('اسم القسم')
                                    ->weight('bold')
                                    ->size('lg'),
                                
                                IconEntry::make('is_active')
                                    ->label('الحالة')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                        
                        TextEntry::make('description')
                            ->label('الوصف')
                            ->placeholder('لا يوجد وصف')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('إعدادات العرض')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ColorEntry::make('color')
                                    ->label('لون القسم'),
                                
                                TextEntry::make('sort_order')
                                    ->label('ترتيب العرض')
                                    ->numeric()
                                    ->badge(),
                                
                                TextEntry::make('lessons_count')
                                    ->label('عدد الدروس')
                                    ->getStateUsing(fn ($record) => $record->lessons()->count())
                                    ->badge()
                                    ->color('primary'),
                            ]),
                    ]),
                
                Section::make('معلومات النظام')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                                
                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
