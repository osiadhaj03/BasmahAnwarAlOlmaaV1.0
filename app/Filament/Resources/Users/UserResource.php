<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\UpdatePassword;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'المستخدمون';
    
    protected static ?string $modelLabel = 'مستخدم';
    
    protected static ?string $pluralModelLabel = 'المستخدمون';
    
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        // إخفاء عن الطلاب ولوحة الطباخ
        if (filament()->getCurrentPanel()?->getId() === 'cook') {
            return false;
        }
        return Auth::check() && Auth::user()->type !== 'student';
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // إذا كان المستخدم معلم، عرض الطلاب المسجلين في دوراته فقط
        if (Auth::check() && Auth::user()->type === 'teacher') {
            $query->where(function ($userQuery) {
                $userQuery->where('type', 'student')
                    ->whereHas('studentLessons', function ($lessonQuery) {
                        $lessonQuery->where('teacher_id', Auth::id());
                    })
                    ->orWhere('id', Auth::id()); // يمكن للمعلم رؤية بياناته الشخصية
            });
        }
        
        return $query;
    }

    public static function canCreate(): bool
    {
        // فقط المدراء يمكنهم إنشاء مستخدمين جدد
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم تعديل جميع المستخدمين
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم تعديل بياناتهم الشخصية فقط
        if ($user->type === 'teacher') {
            return $record->id === $user->id;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // فقط المدراء يمكنهم حذف المستخدمين
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public static function canView(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // المدراء يمكنهم رؤية جميع المستخدمين
        if ($user->type === 'admin') {
            return true;
        }

        // المعلمون يمكنهم رؤية الطلاب المسجلين في دوراتهم وبياناتهم الشخصية
        if ($user->type === 'teacher') {
            if ($record->id === $user->id) {
                return true; // بياناته الشخصية
            }
            
            if ($record->type === 'student') {
                return $record->studentLessons()->where('teacher_id', $user->id)->exists();
            }
        }

        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'updatePassword' => UpdatePassword::route('/{record}/update-password'),
        ];
    }
}
