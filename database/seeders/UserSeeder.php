<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [

            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
            ],

            [
                'name' => 'Seller',
                'email' => 'seller@gmail.com',
                'role' => 'seller',
            ],

            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'role' => 'user',
            ],

        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                ]
            );
        }
    }
}
