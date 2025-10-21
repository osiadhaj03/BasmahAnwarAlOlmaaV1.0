<?php

namespace App\Filament\Resources\Lectures\RelationManagers;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'سجل الحضور';

    protected static ?string $modelLabel = 'حضور';

    protected static ?string $pluralModelLabel = 'سجل الحضور';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الحضور')
                    ->schema([
                        Select::make('user_id')
                            ->label('الطالب')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('status')
                            ->label('حالة الحضور')
                            ->options([
                                'present' => 'حاضر',
                                'absent' => 'غائب',
                                'late' => 'متأخر',
                                'excused' => 'غياب مبرر',
                            ])
                            ->required()
                            ->default('present'),
                        
                        DateTimePicker::make('attended_at')
                            ->label('وقت الحضور')
                            ->native(false),
                        
                        TextInput::make('attendance_code')
                            ->label('رمز الحضور')
                            ->maxLength(10),
                        
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.student_id')
                    ->label('رقم الطالب')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('حالة الحضور')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'excused',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'غياب مبرر',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('attended_at')
                    ->label('وقت الحضور')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('attendance_code')
                    ->label('رمز الحضور')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الحضور')
                    ->options([
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'غياب مبرر',
                    ]),
                
                Tables\Filters\Filter::make('attended_today')
                    ->label('حضر اليوم')
                    ->query(fn (Builder $query): Builder => $query->whereDate('attended_at', today())),
            ])
            ->defaultSort('created_at', 'desc');
    }
}