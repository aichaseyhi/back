<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $user = User::create([
            'name' => 'SuperAdmin',
            'phone' => '20123123',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin'),
            'status' =>'ACTIVE',
            'sexe' => 'male'

        ]);

        $user->assignRole('superadmin');

        $user = User::create([
            'name' => 'admin',
            'phone' => '20123123',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('adminadmin'),
            'status' =>'ACTIVE',
            'sexe' => 'male'

        ]);

        $user->assignRole('admin');
        
    }
}
