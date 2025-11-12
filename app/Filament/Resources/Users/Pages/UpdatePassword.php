<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Schemas\PasswordUpdateForm;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.users.pages.update-password';

    public ?array $data = [];

    /**
     * @var User
     */
    public User $record;

    public function mount(User $record): void
    {
        $this->record = $record;
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return PasswordUpdateForm::configure(Schema::make())->getComponents();
    }

    public function updatePasswordAction(): Action
    {
        return Action::make('updatePassword')
            ->label('تحديث كلمة المرور')
            ->submit()
            ->keyBindings(['mod+s']);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // التحقق من أن المستخدم الحالي له صلاحية تحديث كلمة مرور هذا المستخدم
        if (!Auth::check()) {
            Notification::make()
                ->title('غير مصرح')
                ->body('يجب أن تكون مسجلاً دخولاً')
                ->danger()
                ->send();
            return;
        }

        $currentUser = Auth::user();

        // المدراء يمكنهم تحديث كلمة مرور أي مستخدم
        // المستخدمون يمكنهم تحديث كلمة مرورهم الخاصة فقط
        if ($currentUser->type !== 'admin' && $currentUser->id !== $this->record->id) {
            Notification::make()
                ->title('غير مصرح')
                ->body('ليس لديك صلاحية لتحديث كلمة مرور هذا المستخدم')
                ->danger()
                ->send();
            return;
        }

        try {
            // تحديث كلمة المرور باستخدام الـ hashed cast
            $this->record->update([
                'password' => Hash::make($data['password']),
            ]);

            Notification::make()
                ->title('نجح التحديث')
                ->body('تم تحديث كلمة المرور بنجاح')
                ->success()
                ->send();

            // إعادة التوجيه بعد النجاح
            redirect()->route('filament.admin.resources.users.view', ['record' => $this->record->id]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطأ')
                ->body('حدث خطأ أثناء تحديث كلمة المرور: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->updatePasswordAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'تحديث كلمة المرور - ' . $this->record->name;
    }
}
