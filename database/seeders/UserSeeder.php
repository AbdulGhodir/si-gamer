<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void {
        $data = [
            [
                'nama' => 'Abdul',
                'email' => 'abdul@gmail.com',
                'password' => 'user123',
                'role' => 'customer'
            ],
            [
                'nama' => 'Staff',
                'email' => 'staff@gmail.com',
                'password' => 'staff123',
                'role' => 'staff'
            ],
            [
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
                'role' => 'admin'
            ]
        ];

        foreach ($data as $user) {
            User::create([
                'nama' => $user['nama'],
                'email' => $user['email'],
                'password' => $user['password'],
                'role' => $user['role']
            ]);
        }
    }
}
