<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Super Admin তৈরি করা
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // এই ইমেইল থাকলে আপডেট করবে, না থাকলে নতুন বানাবে
            [
                'name' => 'Super Admin',
                'phone' => '01700000000',
                'password' => Hash::make('123456'), // পাসওয়ার্ড: 123456
                'isAdmin' => 1,
                'isAdminApproved' => 1, // একবারে এপ্রুভড থাকবে
            ]
        );
    }
}
