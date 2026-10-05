<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with ONLY the Super Admin account.
     * All pressings, staff members, services and invoices start at 0 (clean state).
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@pressing.com'],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'pressing_id' => null, // Super Admin is not affiliated with any specific pressing
            ]
        );
    }
}
