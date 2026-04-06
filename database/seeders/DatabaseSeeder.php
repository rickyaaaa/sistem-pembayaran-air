<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::create([
            'username' => 'admin',
            'name'     => 'Administrator SAB',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Seed all 483 residents
        $this->call(ResidentSeeder::class);
    }
}
