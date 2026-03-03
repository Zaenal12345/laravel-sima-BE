<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'dashboard' => ['view'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'brands' => ['view', 'create', 'edit', 'delete'],
            'suppliers' => ['view', 'create', 'edit', 'delete'],
            'customers' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'purchases' => ['view', 'create', 'edit', 'delete'],
            'purchase_invoices' => ['view', 'create', 'edit', 'delete'],
            'wholesale' => ['view', 'create', 'edit', 'delete'],
            'retail' => ['view', 'create', 'edit', 'delete'],
            'stock_in' => ['view', 'create', 'edit', 'delete'],
            'stock_out' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'edit'],
            'reports' => ['view', 'export'],
        ];

        $permissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$module}.{$action}",
                    'display_name' => ucfirst($action) . ' ' . ucfirst($module),
                    'module' => $module,
                    'description' => "Permission to {$action} {$module}",
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Permission::insert($permissions);
    }
}
