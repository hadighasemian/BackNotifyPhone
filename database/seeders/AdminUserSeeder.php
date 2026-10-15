<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');
        if (! $password) {
            throw new \RuntimeException('ADMIN_PASSWORD must be set in .env before seeding.');
        }
        User::updateOrCreate(
            ['email' => 'admin@phonenotify.hi-team.ir'],
            [
                'name' => 'PhoneNotify Admin',
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );
    }
}
