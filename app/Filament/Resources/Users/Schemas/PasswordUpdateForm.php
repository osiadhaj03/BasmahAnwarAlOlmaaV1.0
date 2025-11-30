<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PasswordUpdateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تحديث كلمة المرور')
                    ->description('يرجى إدخال كلمة المرور الجديدة وتأكيدها')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->same('passwordConfirmation', message: 'كلمات المرور غير متطابقة')
                            ->dehydrated()
                            ->rule('regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/')
                            ->validationMessages([
                                'regex' => 'كلمة المرور يجب أن تحتوي على أحرف صغيرة وكبيرة وأرقام',
                            ])
                            ->helperText('يجب أن تحتوي على 8 أحرف على الأقل، أحرف كبيرة وصغيرة وأرقام')
                            ->columnSpan('full'),

                        TextInput::make('passwordConfirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->required()
                            ->dehydrated(false)
                            ->columnSpan('full'),
                    ])
                    ->columnSpan('full')
                    ->columns(1),
            ]);
    }
}
