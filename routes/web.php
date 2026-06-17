<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExposureFactorController;
use App\Http\Controllers\ReferralController;
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

    // ——— Dashboard ———
    Route::get('/panel/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ——— Employees (all authenticated users can manage) ———
    Route::get('/panel/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/panel/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/panel/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/panel/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/panel/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::patch('/panel/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/panel/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // ——— Companies (authenticated) ———
    Route::get('/panel/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/panel/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/panel/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/panel/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/panel/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::patch('/panel/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/panel/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    // ——— Exposure Factors (authenticated) ———
    Route::get('/panel/exposure-factors', [ExposureFactorController::class, 'index'])->name('exposure-factors.index');
    Route::get('/panel/exposure-factors/create', [ExposureFactorController::class, 'create'])->name('exposure-factors.create');
    Route::post('/panel/exposure-factors', [ExposureFactorController::class, 'store'])->name('exposure-factors.store');
    Route::delete('/panel/exposure-factors/{factor}', [ExposureFactorController::class, 'destroy'])->name('exposure-factors.destroy');

    // ——— Referrals (authenticated) ———
    Route::get('/panel/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::get('/panel/referrals/create', [ReferralController::class, 'create'])->name('referrals.create');
    Route::post('/panel/referrals', [ReferralController::class, 'store'])->name('referrals.store');
    Route::get('/panel/referrals/{referral}', [ReferralController::class, 'show'])->name('referrals.show');
    Route::get('/panel/referrals/{referral}/pdf', [ReferralController::class, 'downloadPdf'])->name('referrals.pdf');
    Route::post('/panel/referrals/{referral}/generate-pdf', [ReferralController::class, 'generatePdf'])->name('referrals.generate-pdf');
    Route::delete('/panel/referrals/{referral}', [ReferralController::class, 'destroy'])->name('referrals.destroy');

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
});
