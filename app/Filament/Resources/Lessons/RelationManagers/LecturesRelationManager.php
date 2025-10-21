<?php

namespace App\Filament\Resources\Lessons\RelationManagers;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Lecture;

class LecturesRelationManager extends RelationManager
{
    protected static string $relationship = 'lectures';

    protected static ?string $title = 'المحاضرات';

    protected static ?string $modelLabel = 'محاضرة';

    protected static ?string $pluralModelLabel = 'المحاضرات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات المحاضرة')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان المحاضرة')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('description')
                            ->label('وصف المحاضرة')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        TextInput::make('lecture_number')
                            ->label('رقم المحاضرة')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        
                        DateTimePicker::make('lecture_date')
                            ->label('وقت المحاضرة المجدول')
                            ->required()
                            ->native(false),
                        
                        TextInput::make('duration_minutes')
                            ->label('مدة المحاضرة (بالدقائق)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(60),
                        
                        Select::make('status')
                            ->label('حالة المحاضرة')
                            ->options([
                                'scheduled' => 'مجدولة',
                                'in_progress' => 'جارية',
                                'completed' => 'مكتملة',
                                'cancelled' => 'ملغية',
                                'postponed' => 'مؤجلة',
                            ])
                            ->default('scheduled')
                            ->required(),
                        
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('lecture_number')
                    ->label('رقم المحاضرة')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان المحاضرة')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('موعد المحاضرة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('المدة (دقيقة)')
                    ->suffix(' دقيقة')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'مجدولة',
                        'in_progress' => 'جارية',
                        'completed' => 'مكتملة',
                        'cancelled' => 'ملغية',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('عدد الحضور')
                    ->counts('attendances')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة المحاضرة')
                    ->options([
                        'scheduled' => 'مجدولة',
                        'in_progress' => 'جارية',
                        'completed' => 'مكتملة',
                        'cancelled' => 'ملغية',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة محاضرة جديدة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('lecture_number');
    }
}