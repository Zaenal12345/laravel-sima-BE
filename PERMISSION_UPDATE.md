# Update Permission & Role System

## 🔄 Setelah Perubahan Permission System

Jika Anda sudah menjalankan seeder sebelumnya, Anda perlu refresh database untuk update permission names.

### Option 1: Fresh Migration (Recommended untuk development)

```bash
cd backend

# Drop semua tabel dan migrasi ulang
php artisan migrate:fresh

# Jalankan semua seeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### Option 2: Update Existing Database (Untuk production)

```bash
cd backend

# Hapus permission dan role yang lama
php artisan tinker
```

Di dalam tinker:
```php
// Hapus semua data
App\Models\User::where('id', '>', 1)->delete();  // Jangan hapus super admin
App\Models\Role::where('id', '>', 0)->delete();
App\Models\Permission::where('id', '>', 0)->delete();

// Exit tinker
exit
```

Lalu jalankan seeder:
```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### Option 3: Manual Update (Jika ada data user lain)

Jika Anda sudah punya user production data, jangan hapus. Cukup update permission names:

```bash
php artisan tinker
```

Di dalam tinker:
```php
// Update permissions names untuk module 'merks' -> 'brands'
App\Models\Permission::where('name', 'like', 'merks.%')->update([
    'name' => DB::raw("REPLACE(name, 'merks.', 'brands.')"),
    'module' => 'brands'
]);

// Update permissions names untuk module 'purchase' -> 'purchases' (untuk PO)
App\Models\Permission::where('name', 'like', 'purchase.%')
    ->where('name', 'not like', 'purchase_invoices.%')
    ->update([
        'name' => DB::raw("REPLACE(name, 'purchase.', 'purchases.')"),
        'module' => 'purchases'
    ]);

// Add new permissions for purchase_invoices, stock_in, stock_out, settings
$modules = [
    'purchase_invoices' => ['view', 'create', 'edit', 'delete'],
    'stock_in' => ['view', 'create', 'edit', 'delete'],
    'stock_out' => ['view', 'create', 'edit', 'delete'],
    'settings' => ['view', 'edit'],
];

foreach ($modules as $module => $actions) {
    foreach ($actions as $action) {
        App\Models\Permission::firstOrCreate(
            ['name' => "{$module}.{$action}"],
            [
                'display_name' => ucfirst($action) . ' ' . ucfirst(str_replace('_', ' ', $module)),
                'module' => $module,
                'description' => "Permission to {$action} {$module}",
                'status' => 'active'
            ]
        );
    }
}

exit
```

## ✅ Verifikasi Setup

Setelah menjalankan perintah di atas, verifikasi dengan:

### 1. Cek Permissions
```bash
php artisan tinker
```

```php
// Cek semua permissions
App\Models\Permission::orderBy('module')->get(['id', 'name', 'module']);

// Expected output: should include:
// - dashboard.view
// - users.view, users.create, users.edit, users.delete
// - roles.view, roles.create, roles.edit, roles.delete
// - permissions.view, permissions.create, permissions.edit, permissions.delete
// - categories.view, categories.create, categories.edit, categories.delete
// - brands.view, brands.create, brands.edit, brands.delete
// - suppliers.view, suppliers.create, suppliers.edit, suppliers.delete
// - customers.view, customers.create, customers.edit, customers.delete
// - products.view, products.create, products.edit, products.delete
// - purchases.view, purchases.create, purchases.edit, purchases.delete
// - purchase_invoices.view, purchase_invoices.create, purchase_invoices.edit, purchase_invoices.delete
// - wholesale.view, wholesale.create, wholesale.edit, wholesale.delete
// - retail.view, retail.create, retail.edit, retail.delete
// - stock_in.view, stock_in.create, stock_in.edit, stock_in.delete
// - stock_out.view, stock_out.create, stock_out.edit, stock_out.delete
// - settings.view, settings.edit
// - reports.view, reports.export

exit
```

### 2. Cek Roles
```bash
php artisan tinker
```

```php
// Cek roles dengan permissions count
$roles = App\Models\Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "{$role->display_name}: {$role->permissions->count()} permissions\n";
}

// Expected output:
// Super Administrator: ~50+ permissions
// Administrator: ~30+ permissions
// Cashier: 4 permissions
// Warehouse Staff: 6 permissions

exit
```

### 3. Cek Users
```bash
php artisan tinker
```

```php
// Cek users dengan roles
$users = App\Models\User::with('roles')->get();
foreach ($users as $user) {
    $roleNames = $user->roles->pluck('name')->implode(', ');
    echo "{$user->email}: {$roleNames}\n";
}

// Expected output:
// admin@sima.com: super_admin
// administrator@sima.com: admin
// cashier@sima.com: cashier
// warehouse@sima.com: warehouse

exit
```

## 🧪 Test Login dengan Berbagai Role

### Test sebagai Super Admin
```json
{
  "email": "admin@sima.com",
  "password": "password"
}
```
Expected: Bisa melihat SEMUA menu

### Test sebagai Admin
```json
{
  "email": "administrator@sima.com",
  "password": "password"
}
```
Expected: Bisa melihat Dashboard, Product Management (Category, Brand, Supplier, Customer, Product), Purchase, Wholesale, Retail, In Stock, Out Stock
TIDAK bisa melihat: User Management

### Test sebagai Cashier
```json
{
  "email": "cashier@sima.com",
  "password": "password"
}
```
Expected: Bisa melihat Dashboard, Retail Sales, Product (view only)

### Test sebagai Warehouse
```json
{
  "email": "warehouse@sima.com",
  "password": "password"
}
```
Expected: Bisa melihat Dashboard, In Stock, Out Stock, Product (view only)

## 🐛 Troubleshooting

### Error: "Table 'roles' not found"
```bash
php artisan migrate
```

### Error: "Permission not found"
```bash
php artisan db:seed --class=PermissionSeeder
```

### Login berhasil tapi menu tidak muncul
1. Cek response dari login endpoint
2. Pastikan ada field `permissions` di response
3. Cek browser console untuk error
4. Clear localStorage:
   ```javascript
   // Di browser console
   localStorage.clear();
   location.reload();
   ```

### Semua menu muncul padahal sudah di-assign role
1. Pastikan `AuthContext` yang benar digunakan (dari `../contexts/AuthContext.tsx`)
2. Cek file `src/app/App.tsx`:
   ```typescript
   import { AuthProvider } from "../contexts/AuthContext";  // ← HARUS "contexts" (plural)
   ```
3. Pastikan user permissions ada:
   ```javascript
   // Di browser console
   const user = JSON.parse(localStorage.getItem('user'));
   console.log(user.permissions);  // Harus array of strings
   ```

## 📝 Dokumentasi Lengkap

Untuk informasi lebih lengkap tentang permission system, baca:
- [frontend/PERMISSION_SYSTEM.md](../frontend/PERMISSION_SYSTEM.md)
