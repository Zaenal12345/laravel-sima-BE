<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
            'description' => 'Full access to all system features',
            'status' => 'active',
        ]);
        $superAdmin->permissions()->attach(Permission::pluck('id'));

        // Admin - Most permissions except user management
        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Access to manage products, categories, suppliers, customers',
            'status' => 'active',
        ]);

        $adminPermissions = Permission::where(function ($q) {
            $q->where('module', 'dashboard')
              ->orWhere('module', 'categories')
              ->orWhere('module', 'brands')
              ->orWhere('module', 'suppliers')
              ->orWhere('module', 'customers')
              ->orWhere('module', 'products')
              ->orWhere('module', 'purchases')
              ->orWhere('module', 'purchase_invoices')
              ->orWhere('module', 'wholesale')
              ->orWhere('module', 'retail')
              ->orWhere('module', 'stock_in')
              ->orWhere('module', 'stock_out')
              ->orWhere('module', 'reports');
        })->pluck('id');

        $admin->permissions()->attach($adminPermissions);

        // Cashier - Limited permissions
        $cashier = Role::create([
            'name' => 'cashier',
            'display_name' => 'Cashier',
            'description' => 'Access to retail sales only',
            'status' => 'active',
        ]);

        $cashierPermissions = Permission::where(function ($q) {
            $q->where('name', 'dashboard.view')
              ->orWhere('name', 'retail.view')
              ->orWhere('name', 'retail.create')
              ->orWhere('name', 'products.view');
        })->pluck('id');

        $cashier->permissions()->attach($cashierPermissions);

        // Warehouse - Limited permissions
        $warehouse = Role::create([
            'name' => 'warehouse',
            'display_name' => 'Warehouse Staff',
            'description' => 'Access to stock management',
            'status' => 'active',
        ]);

        $warehousePermissions = Permission::where(function ($q) {
            $q->where('name', 'dashboard.view')
              ->orWhere('name', 'stock_in.view')
              ->orWhere('name', 'stock_in.create')
              ->orWhere('name', 'stock_out.view')
              ->orWhere('name', 'stock_out.create')
              ->orWhere('name', 'products.view');
        })->pluck('id');

        $warehouse->permissions()->attach($warehousePermissions);
    }
}
