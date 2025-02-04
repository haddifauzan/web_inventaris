<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create('tbl_user')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'kode_reset' => 'ADMIN-19123792',
        ]);
    }
}
