<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with ONLY the Super Admin account.
     * All pressings, staff members, services and invoices start at 0 (clean state).
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        User::create([
            'name' => 'Super Administrateur',
            'email' => 'admin@pressing.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'pressing_id' => null,
        ]);
    }
}
