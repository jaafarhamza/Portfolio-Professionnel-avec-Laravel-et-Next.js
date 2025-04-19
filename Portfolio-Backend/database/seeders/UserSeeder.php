<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Hamza Jaafar',
            'email' => 'jaafar.hamza711@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => 'Full-stack developer with a passion for web technologies.',
        ]);
    }
}