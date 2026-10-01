<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('auth.initial_admin.name');
        $email = config('auth.initial_admin.email');
        $password = config('auth.initial_admin.password');

        if (! filled($name) || ! filled($email) || ! filled($password)) {
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 16) {
            throw new InvalidArgumentException('Initial admin email must be valid and password must be at least 16 characters.');
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'status' => true,
                'type' => 'super_admin',
            ],
        );

        $admin->forceFill(['type' => 'super_admin', 'status' => true])->save();
        $admin->syncRoles(['Super Admin']);
    }
}
