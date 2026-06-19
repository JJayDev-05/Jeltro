<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@jeltro.com'],
            [
                'name'     => 'Jeltro Admin',
                'password' => Hash::make('jeltro123'),
                'is_admin' => true,
            ]
        );
    }
}
