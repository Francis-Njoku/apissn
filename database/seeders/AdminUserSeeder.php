<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            $this->command->error('ADMIN_PASSWORD is not set in .env');
            return;
        }

        // Check if user already exists
        if (User::where('email', 'admin@email.com')->exists()) {
            $this->command->info('Admin user already exists');
            return;
        }

        User::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@email.com',
            'password' => Hash::make($password),
        ]);

        $this->command->info('Admin user created successfully');
    }
}
