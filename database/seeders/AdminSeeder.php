<?php

namespace Database\Seeders;

use App\Constants\AdminStatus;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a super admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'mobile' => '+8801712345678',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => AdminStatus::ACTIVE,
        ]);

        // Create additional admin users
        $admins = [
            [
                'name' => 'John Doe',
                'email' => 'admin@gmail.com',
                'mobile' => '+8801812345678',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => AdminStatus::ACTIVE,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'admin1@gmail.com',
                'mobile' => '+8801912345678',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => AdminStatus::ACTIVE,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin2@gmail.com',
                'mobile' => null,
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'status' => AdminStatus::ACTIVE,
            ],
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
