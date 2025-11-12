<?php

namespace App\Filament\Resources\Users\Actions;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'updatePassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('تحديث كلمة السر')
            ->icon('heroicon-m-key')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('تحديث كلمة السر للمستخدمين المحددين')
            ->modalDescription('أدخل كلمة السر الجديدة سيتم تطبيقها على جميع المستخدمين المحددين')
            ->modalSubmitActionLabel('تحديث')
            ->modalCancelActionLabel('إلغاء')
            ->form([
                TextInput::make('password')
                    ->label('كلمة السر الجديدة')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->helperText('يجب أن تكون كلمة السر على الأقل 8 أحرف'),
                
                TextInput::make('passwordConfirmation')
                    ->label('تأكيد كلمة السر')
                    ->password()
                    ->required()
                    ->same('password')
                    ->dehydrated(false),
            ])
            ->action(function (Collection $records, array $data): void {
                try {
                    $records->each(function ($record) use ($data) {
                        $record->update([
                            'password' => Hash::make($data['password']),
                        ]);
                    });

                    Notification::make()
                        ->title('تم التحديث بنجاح')
                        ->body('تم تحديث كلمة السر لـ ' . $records->count() . ' مستخدم')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('حدث خطأ')
                        ->body('فشل تحديث كلمة السر: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
