<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@sima.com',
            'password' => bcrypt('password'),
        ]);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->attach($superAdminRole);
        }

        // Create regular Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'administrator@sima.com',
            'password' => bcrypt('password'),
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->roles()->attach($adminRole);
        }

        // Create Cashier User
        $cashier = User::create([
            'name' => 'Cashier Staff',
            'email' => 'cashier@sima.com',
            'password' => bcrypt('password'),
        ]);

        $cashierRole = Role::where('name', 'cashier')->first();
        if ($cashierRole) {
            $cashier->roles()->attach($cashierRole);
        }

        // Create Warehouse User
        $warehouse = User::create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@sima.com',
            'password' => bcrypt('password'),
        ]);

        $warehouseRole = Role::where('name', 'warehouse')->first();
        if ($warehouseRole) {
            $warehouse->roles()->attach($warehouseRole);
        }
    }
}
