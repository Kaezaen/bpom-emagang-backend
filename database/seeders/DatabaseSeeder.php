<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            DivisiSeeder::class,
        ]);

        // Default Admin User
        User::create([
            'name' => 'Admin BPOM',
            'email' => 'admin@bpom.go.id',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
            'is_active' => 1,
        ]);
    }
}
