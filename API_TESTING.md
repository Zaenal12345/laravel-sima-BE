# Testing User Management API Endpoints

## Available Endpoints

### 1. Get Roles (for Dropdown)
**Endpoint:** `GET /api/users/roles`
**Auth:** Required (Bearer Token)
**Description:** Mendapatkan daftar roles aktif untuk assign ke user

**Postman Setup:**
```
Method: GET
URL: http://localhost:8000/api/users/roles
Headers:
  Authorization: Bearer <your_token>
  Accept: application/json
```

**Expected Response:**
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
    },
    {
      "id": 3,
      "name": "cashier",
      "display_name": "Cashier"
    },
    {
      "id": 4,
      "name": "warehouse",
      "display_name": "Warehouse Staff"
    }
  ],
  "message": "Roles retrieved successfully"
}
```

---

### 2. Get Users List
**Endpoint:** `GET /api/users`
**Auth:** Required (Bearer Token)
**Query Params:**
- `page` (optional) - Page number (default: 1)
- `per_page` (optional) - Items per page (default: 10)
- `search` (optional) - Search by name or email
- `role` (optional) - Filter by role name
- `status` (optional) - "active" or "inactive"

**Postman Setup:**
```
Method: GET
URL: http://localhost:8000/api/users?page=1&per_page=10
Headers:
  Authorization: Bearer <your_token>
  Accept: application/json
```

---

### 3. Create User
**Endpoint:** `POST /api/users`
**Auth:** Required (Bearer Token)

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role_ids": [2, 3]
}
```

---

### 4. Update User
**Endpoint:** `PUT /api/users/{id}`
**Auth:** Required (Bearer Token)

**Request Body:**
```json
{
  "name": "John Doe Updated",
  "email": "john.updated@example.com",
  "role_ids": [2]
}
```

---

## How to Get Bearer Token

### 1. Login First
**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "admin@sima.com",
  "password": "password"
}
```

**Response:**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "Super Administrator",
      "email": "admin@sima.com",
      "roles": ["super_admin"],
      "permissions": ["dashboard.view", "users.view", ...]
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

### 2. Copy the Token
Copy token from response and use it in Authorization header:

```
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

## Troubleshooting

### "Endpoint not found" Error:

1. **Check if route is registered:**
```bash
php artisan route:list --path=users/roles
```

Expected output:
```
GET|HEAD| users/roles | users.roles | App\Http\Controllers\Api\UserController@roles
```

2. **Clear route cache:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

3. **Check API URL:**
   - Make sure you're accessing: `http://localhost:8000/api/users/roles`
   - NOT `http://localhost:8000/users/roles` (missing /api)

4. **Check Auth:**
   - Make sure you're sending `Authorization: Bearer <token>` header
   - Token must be from login response

---

## Quick Test Sequence in Postman:

1. **Login** → Get token
2. **Get Roles** → Test `/api/users/roles`
3. **Get Users** → Test `/api/users`
4. **Create User** → Test POST `/api/users`
