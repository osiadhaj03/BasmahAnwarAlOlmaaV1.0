<?php

namespace App\Filament\Resources\Lessons\RelationManagers;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $title = 'الطلاب المسجلين';

    protected static ?string $modelLabel = 'طالب';

    protected static ?string $pluralModelLabel = 'الطلاب';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات تسجيل الطالب')
                    ->schema([
                        Select::make('user_id')
                            ->label('الطالب')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('enrollment_status')
                            ->label('حالة التسجيل')
                            ->options([
                                'enrolled' => 'مسجل',
                                'pending' => 'في الانتظار',
                                'dropped' => 'منسحب',
                                'completed' => 'مكتمل',
                            ])
                            ->default('enrolled')
                            ->required(),
                        
                        DatePicker::make('enrolled_at')
                            ->label('تاريخ التسجيل')
                            ->default(now())
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
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('pivot.enrollment_status')
                    ->label('حالة التسجيل')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        'completed' => 'info',
                        'dropped' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'pending' => 'في الانتظار',
                        'suspended' => 'معلق',
                        'completed' => 'مكتمل',
                        'dropped' => 'منسحب',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('pivot.enrolled_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('عدد مرات الحضور')
                    ->counts([
                        'attendances' => fn (Builder $query) => $query->whereHas('lecture', function (Builder $query) {
                            $query->where('lesson_id', $this->getOwnerRecord()->id);
                        })
                    ])
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('academic_level')
                    ->label('المستوى الأكاديمي')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => 'مبتدئ',
                        'intermediate' => 'متوسط',
                        'advanced' => 'متقدم',
                        'expert' => 'خبير',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('pivot.notes')
                    ->label('ملاحظات')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('enrollment_status')
                    ->label('حالة التسجيل')
                    ->relationship('pivot', 'enrollment_status')
                    ->options([
                        'active' => 'نشط',
                        'pending' => 'في الانتظار',
                        'suspended' => 'معلق',
                        'completed' => 'مكتمل',
                        'dropped' => 'منسحب',
                    ]),
                
                Tables\Filters\SelectFilter::make('academic_level')
                    ->label('المستوى الأكاديمي')
                    ->options([
                        'beginner' => 'مبتدئ',
                        'intermediate' => 'متوسط',
                        'advanced' => 'متقدم',
                        'expert' => 'خبير',
                    ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'student'));
    }
}