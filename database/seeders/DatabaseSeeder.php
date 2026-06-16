<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin awal (pemilik). GANTI password setelah login pertama.
        User::updateOrCreate(
            ['email' => 'admin@tolaki.test'],
            [
                'name' => 'Admin Tolaki',
                'password' => Hash::make('password'),
                'role' => Role::Admin,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'moderator@tolaki.test'],
            [
                'name' => 'Moderator Tolaki',
                'password' => Hash::make('password'),
                'role' => Role::Moderator,
                'email_verified_at' => now(),
            ],
        );
    }
}
