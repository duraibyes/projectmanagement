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
        $seededAdminEmail = 'superadmin_project@yopmail.com';
        $user = User::where('email', '=', $seededAdminEmail)->first();
        if ($user === null) {
            $user = User::create([
                'name'                          => 'Admin',
                'email'                         => $seededAdminEmail,
                'password'                      => Hash::make('password'),
                'is_super_admin'                => 1
            ]);
        }
    }
}
