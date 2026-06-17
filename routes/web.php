<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/panel/employees');
    }
    return view('auth.login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ——— Employees ———
    // View-only: 'employee' role (can list and view employees)
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/panel/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/panel/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    });

    // Full CRUD: 'manager' role (create, edit, delete employees too)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/panel/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/panel/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/panel/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::patch('/panel/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/panel/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // ——— Users & Roles (admin only) ———
    Route::middleware(['role:admin'])->group(function () {

        // Users CRUD
        Route::get('/panel/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/panel/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/panel/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/panel/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/panel/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/panel/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/panel/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Roles CRUD
        Route::get('/panel/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/panel/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::delete('/panel/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // ——— Employees: admin can also do full CRUD (admin is all-powerful) ———
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/panel/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/panel/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/panel/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::patch('/panel/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/panel/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
});
