<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'name'           => 'Super Admin',
            'email'          => 'admin@courierportal.com',
            'phone'          => null,
            'password'       => Hash::make('Admin@123456'),
            'role'           => 'admin',
            'is_active'      => true,
            'remember_token' => null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $this->command->info('✅ Super Admin seeded: admin@courierportal.com / Admin@123456');
    }
}
