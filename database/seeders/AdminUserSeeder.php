<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $pharmacistRole = Role::firstOrCreate(['name' => 'pharmacist']);
        $deliveryPersonRole = Role::firstOrCreate(['name' => 'delivery_person']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@pharmacy.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $admin->assignRole($adminRole);
    }
}
