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
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use App\Models\AttendanceCode;

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
                        Select::make('student_id')
                            ->label('الطالب')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} - {$record->email}"),
                        
                        Select::make('attendance_code_id')
                            ->label('رمز الحضور')
                            ->relationship('attendanceCode', 'code')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (AttendanceCode $record): string => "{$record->code} - {$record->lesson->title}"),
                        
                        Select::make('status')
                            ->label('حالة الحضور')
                            ->options([
                                'present' => 'حاضر',
                                'absent' => 'غائب',
                                'late' => 'متأخر',
                                'excused' => 'غياب مبرر',
                            ])
                            ->default('present')
                            ->required(),
                        
                        DateTimePicker::make('attended_at')
                            ->label('وقت الحضور')
                            ->default(now())
                            ->native(false),
                        
                        TextInput::make('ip_address')
                            ->label('عنوان IP')
                            ->maxLength(45),
                        
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
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('student.email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('attendanceCode.code')
                    ->label('رمز الحضور')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('حالة الحضور')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'absent' => 'danger',
                        'excused' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'حاضر',
                        'absent' => 'غائب',
                        'late' => 'متأخر',
                        'excused' => 'غياب مبرر',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('attended_at')
                    ->label('وقت الحضور')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('عنوان IP')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
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
                
                Tables\Filters\Filter::make('attended_this_week')
                    ->label('حضر هذا الأسبوع')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('attended_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('تسجيل حضور جديد'),
            ])
            ->actions([
                EditAction::make()
                    ->label('تعديل'),
                
                DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('attended_at', 'desc');
    }
}