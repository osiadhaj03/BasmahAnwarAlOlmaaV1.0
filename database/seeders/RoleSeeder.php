<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'مدير',
                'slug' => 'admin',
                'description' => 'مدير النظام - صلاحيات كاملة',
                'color' => 'danger',
            ],
            [
                'name' => 'معلم',
                'slug' => 'teacher',
                'description' => 'معلم - إدارة الدروس والحضور',
                'color' => 'warning',
            ],
            [
                'name' => 'طالب',
                'slug' => 'student',
                'description' => 'طالب - الوصول لمحتوى التعلم',
                'color' => 'success',
            ],
            [
                'name' => 'زبون',
                'slug' => 'customer',
                'description' => 'زبون المطبخ - طلب الوجبات',
                'color' => 'info',
            ],
            [
                'name' => 'طباخ',
                'slug' => 'cook',
                'description' => 'طباخ - إدارة الوجبات والتوصيل',
                'color' => 'primary',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
