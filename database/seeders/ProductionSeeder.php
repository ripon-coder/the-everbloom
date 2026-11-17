<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('ADMIN_SEED_EMAIL', ''));
        $password = (string) env('ADMIN_SEED_PASSWORD', '');
        $name = trim((string) env('ADMIN_SEED_NAME', 'Store Administrator'));

        if ($email === '' || $password === '') {
            throw new RuntimeException(
                'ADMIN_SEED_EMAIL and ADMIN_SEED_PASSWORD must be set in the environment before running ProductionSeeder.'
            );
        }

        if ($password === 'password') {
            throw new RuntimeException(
                'Refusing to seed a known default password ("password") in ProductionSeeder.'
            );
        }

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name !== '' ? $name : 'Store Administrator',
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $this->call(BangladeshLocationSeeder::class);
    }
}

