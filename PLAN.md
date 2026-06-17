# HR Portal - Project Plan

## Overview
A Docker-based Laravel application with PHP-FPM, Nginx, and MySQL.
Frontend uses Bootstrap 5. Core functionality: manage Employees through a User panel.

---

## Architecture

```
┌─────────────────────────────────────────────────┐
│  Docker Compose                                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────────┐  │
│  │  Nginx   │→│ PHP-FPM  │→│    MySQL       │  │
│  │ :8080    │ │ :9000    │ │ :3306          │  │
│  └──────────┘  └──────────┘  └──────────────┘  │
└─────────────────────────────────────────────────┘
```

---

## Step 1 — Docker Infrastructure

### 1.1 Docker Compose (`docker-compose.yml`)
- `laravel_app` service: PHP 8.3 FPM, Laravel workspace image
- `nginx` service: Nginx reverse proxy on port 8080
- `mysql` service: MySQL 8.0 on port 3306
- Volumes: host code → container `/var/www/html`
- `.env` configuration via `laravel_app`

### 1.2 Dockerfile (`Dockerfile`)
- Multi-stage build or simple `php:8.3-fpm`
- Install extensions: `pdo_mysql`, `gd`, `zip`, `xml`, `ctype`, `curl`, `mbstring`, `bcmath`
- Set `DOCUMENT_ROOT=/var/www/html/public`
- Copy composer files, install deps, copy source

### 1.3 Nginx Config (`docker/nginx/default.conf`)
- Root → `/var/www/html/public`
- PHP-FPM pass to `laravel_app:9000`
- Rewrite rules for Laravel (try_files)
- Static asset caching

### 1.4 Environment (`.env.docker`)
- Database credentials matching Docker MySQL

---

## Step 2 — Laravel Application Bootstrap

### 2.1 Initialize Laravel Project
- `composer create-project laravel/laravel .`
- Configure `app.php` for timezone, locale
- Set up `.env` from `.env.docker`

### 2.2 Configure `.env`
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=hrportal
DB_USERNAME=hruser
DB_PASSWORD=hrpassword
```

### 2.3 Laravel Sanctum Setup (optional)
- We'll use session-based auth (simpler, no SPA needed)
- Session guard with `web` middleware

---

## Step 3 — Database Schema & Migrations

### 3.1 Core Tables

#### `roles`
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| name | VARCHAR(255) | unique |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| name | VARCHAR(255) | |
| email | VARCHAR(255) | unique |
| password | VARCHAR(255) | bcrypt |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `user_roles` (pivot)
| Column | Type | Notes |
|--------|------|-------|
| user_id | BIGINT UNSIGNED FK→users | |
| role_id | BIGINT UNSIGNED FK→roles | |
| Primary key: (user_id, role_id) | | |

#### `employees`
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| first_name | VARCHAR(255) | |
| last_name | VARCHAR(255) | |
| email | VARCHAR(255) | optional unique |
| position | VARCHAR(255) | |
| department | VARCHAR(255) | |
| phone | VARCHAR(50) | optional |
| hire_date | DATE | optional |
| salary | DECIMAL(10,2) | optional |
| status | ENUM('active','inactive','terminated') | default 'active' |
| created_by | BIGINT UNSIGNED FK→users | nullable |
| updated_by | BIGINT UNSIGNED FK→users | nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### 3.4 Seeders
- **AdminRoleSeeder**: Create "Admin" role
- **UserSeeder**: Create default admin user with email `admin@hrportal.local`, password `password`
- **DatabaseSeeder**: Run all seeders

---

## Step 4 — Authentication

### 4.1 Auth System (Session-based)
- Use Laravel's built-in authentication scaffolding
- Login: email + password
- Register: optional (or only admins can create users)
- Use `Auth` facade

### 4.2 Authentication Routes
```
POST  /login       → LoginController@store
POST  /logout      → LoginController@destroy
GET   /login       → LoginController@show
GET   /register    → (optional)
```

### 4.3 Middleware
- `auth` — requires login
- `role:admin` — requires at least one "admin" role

---

## Step 5 — Roles System

### 5.1 Models
- `App\Models\Role` — belongsToMany User via `userRoles`
- `App\Models\User` — belongsToMany Role via `roles`, hasMany Employee via `createdEmployees`

### 5.2 Roles Controller
- `RoleController@index` — list all roles
- `RoleController@show` — view role details (users with this role)
- `RoleController@destroy` — delete role (check users first)

### 5.3 User-Role Management
- `UserRoleController@store` — assign role to user
- `UserRoleController@destroy` — remove role from user

### 5.4 Middleware
- `RoleMiddleware` — checks `auth()->user()->roles()->where('name', $role)->exists()`

### 5.5 Admin-only routes
- `/panel/roles` — manage roles
- `/panel/users` — manage users (assign/remove roles)

---

## Step 6 — Users Panel

### 6.1 Users Controller
- `UserController@index` — list all users (paginated)
- `UserController@show` — view user, their roles
- `UserController@create` — form for new user
- `UserController@store` — validate and save user
- `UserController@edit` — edit existing user
- `UserController@update` — update user
- `UserController@destroy` — delete user

### 6.2 User Management Routes (Admin only)
```
GET   /panel/users          → UserController@index
GET   /panel/users/create   → UserController@create
POST  /panel/users          → UserController@store
GET   /panel/users/{user}   → UserController@show
GET   /panel/users/{user}/edit → UserController@edit
PATCH /panel/users/{user}   → UserController@update
DELETE /panel/users/{user}  → UserController@destroy
```

---

## Step 7 — Employees Panel

### 7.1 Employees Controller
- `EmployeeController@index` — list all employees (search + paginate)
- `EmployeeController@show` — view employee details
- `EmployeeController@create` — form for new employee
- `EmployeeController@store` — validate and save employee
- `EmployeeController@edit` — edit existing employee
- `EmployeeController@update` — update employee
- `EmployeeController@destroy` — delete employee

### 7.2 Employee Routes (Authenticated)
```
GET   /panel/employees          → EmployeeController@index
GET   /panel/employees/create   → EmployeeController@create
POST  /panel/employees          → EmployeeController@store
GET   /panel/employees/{employee}  → EmployeeController@show
GET   /panel/employees/{employee}/edit → EmployeeController@edit
PATCH /panel/employees/{employee}    → EmployeeController@update
DELETE /panel/employees/{employee}   → EmployeeController@destroy
```

---

## Step 8 — Frontend (Bootstrap 5)

### 8.1 Layout
- `resources/views/layouts/app.blade.php` — master layout with:
  - Bootstrap 5 CDN (or npm)
  - Navbar: "HR Portal", login/register links, logged-in user dropdown
  - Sidebar (for logged-in users): "Dashboard", "Employees", "Users (Admin only)", "Roles (Admin only)"
  - Flash messages area

### 8.2 Auth Views
- `resources/views/auth/login.blade.php` — email + password form
- `resources/views/auth/register.blade.php` — name + email + password (optional)

### 8.3 Panel Views — Users
- `resources/views/panel/users/index.blade.php` — table with name, email, roles, actions
- `resources/views/panel/users/create.blade.php` — form
- `resources/views/panel/users/edit.blade.php` — form
- `resources/views/panel/users/show.blade.php` — details + assigned roles

### 8.4 Panel Views — Roles
- `resources/views/panel/roles/index.blade.php` — list of roles
- `resources/views/panel/roles/show.blade.php` — role + assigned users

### 8.5 Panel Views — Employees
- `resources/views/panel/employees/index.blade.php` — table with search, name, position, department, status, actions
- `resources/views/panel/employees/create.blade.php` — form
- `resources/views/panel/employees/edit.blade.php` — form
- `resources/views/panel/employees/show.blade.php` — employee details

---

## Step 9 — Routing & Middleware Summary

### Routes (`routes/web.php`)
```php
// Public
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Authenticated
Route::middleware(['auth'])->group(function () {
    Route::get('/panel/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/panel/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/panel/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/panel/employees/{employee}', [Employee::class, 'show'])->name('employees.show');
    Route::get('/panel/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::patch('/panel/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/panel/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Admin-only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/panel/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/panel/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/panel/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/panel/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/panel/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/panel/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/panel/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/panel/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/panel/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::delete('/panel/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
```

---

## Implementation Order

| Step | Task |
|------|------|
| 1 | Create plan (this file) | [x] |
| 2 | Docker infrastructure (Dockerfile, docker-compose.yml, nginx config) | [x] |
| 3 | Initialize Laravel project with Composer | [x] |
| 4 | Create database migrations (roles, users, user_roles, employees) | [x] |
| 5 | Create Eloquent models | [x] |
| 6 | Create seeders (admin role, default admin user) | [x] |
| 7 | Set up authentication (LoginController, routes, views) | [x] |
| 8 | Create RoleMiddleware | [x] |
| 9 | Create RoleController + views | [x] |
| 10 | Create UserController + views | [x] |
| 11 | Create Employee model + Controller + views | [x] |
| 12 | Create Blade layouts with Bootstrap 5 | [x] |
| 13 | Final testing (docker compose up, migrate, seed, verify) | [x] |
