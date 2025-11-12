<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            EditAction::make(),
        ];

        // إضافة زر تحديث كلمة المرور للمدراء والمستخدم الخاص به
        if (Auth::check() && (Auth::user()->type === 'admin' || Auth::user()->id === $this->record->id)) {
            $actions[] = Action::make('updatePassword')
                ->label('تحديث كلمة المرور')
                ->icon('heroicon-m-lock-closed')
                ->color('primary')
                ->url(route('filament.admin.resources.users.updatePassword', ['record' => $this->record]));
        }

        $actions[] = DeleteAction::make();

        return $actions;
    }
}
