<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'name'     => 'Administrator SAB',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Create pengurus
        User::create([
            'username' => 'pengurus',
            'name'     => 'Pengurus SAB',
            'password' => Hash::make('pengurus123'),
            'role'     => 'pengurus',
        ]);

        // Seed all 483 residents
        $this->call(ResidentSeeder::class);
    }
}
