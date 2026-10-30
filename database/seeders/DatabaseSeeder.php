<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the main (super) admin.
     * Set SEED_ADMIN_EMAIL and SEED_ADMIN_PASSWORD in .env first.
     * If no password is set, a random one is generated and printed once.
     */
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'admin@example.com');
        $password = env('SEED_ADMIN_PASSWORD');

        if (! $password) {
            $password = Str::password(14);
            $this->command?->warn("No SEED_ADMIN_PASSWORD set. Generated admin password: {$password}");
            $this->command?->warn('Save it now and change it after your first login.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'phone' => '01700000000',
                'password' => Hash::make($password),
                'isAdmin' => 1,
                'isAdminApproved' => 1,
                'isBlocked' => 0,
            ]
        );
    }
}
