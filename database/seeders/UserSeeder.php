<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'surname'  => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('1234'),
                'email'    => 'admin@example.com',
                'status'   => 'AKTIF',
                'role'     => 4,
            ]
        ]);
    }
}
