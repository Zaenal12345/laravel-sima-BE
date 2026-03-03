# Setup Roles untuk User Management

## Langkah-langkah untuk menyelesaikan masalah "Unable to load roles":

### 1. Jalankan Migrations
```bash
cd backend
php artisan migrate
```

### 2. Jalankan Seeders untuk Role dan Permission
```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### 3. Verifikasi Database
Pastikan tabel berikut ada dan memiliki data:
- `roles` - tabel roles
- `permissions` - tabel permissions
- `permission_role` - relasi permission ke role
- `role_user` - relasi role ke user

### 4. Test API Endpoint
```bash
php artisan tinker
```

Di dalam tinker:
```php
// Cek roles
App\Models\Role::all();

// Cek user dengan roles
App\Models\User::with('roles')->first();
```

## Test Endpoint via Postman/cURL:
```bash
curl -X GET http://localhost:8000/api/users/roles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

Expected Response:
```json
{
  "data": [
    {
      "id": 1,
      "name": "super_admin",
      "display_name": "Super Administrator"
    },
    {
      "id": 2,
      "name": "admin",
      "display_name": "Administrator"
    }
  ],
  "message": "Roles retrieved successfully"
}
```

## Troubleshooting:

### Jika muncul error "Table 'roles' not found":
Jalankan: `php artisan migrate`

### Jika roles kosong:
Jalankan: `php artisan db:seed --class=RoleSeeder`

### Jika masih error:
1. Cek koneksi database di `.env`
2. Clear config: `php artisan config:clear`
3. Clear cache: `php artisan cache:clear`
