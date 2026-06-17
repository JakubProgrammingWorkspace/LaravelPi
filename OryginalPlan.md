# HR Portal - Project Plan

> **Project:** HR Portal — A Polish HR management application (Users & flexible Roles, Companies, Employees, occupational exposure factors catalogue, and standardized medical examination referrals with PDF generation).
> **Tech Stack:** Docker-based Laravel application with PHP-FPM, Nginx, MySQL. Frontend uses Bootstrap 5.
>
> **Important:** The UI labels, form fields, error messages, and entity properties are in **Polish**. All views visible to users must be translated to Polish. Keep this in mind when building the frontend.

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
- Configure `app.php` for timezone, locale (`pl_PL`)
- Set up `.env` from `.env.docker`

### 2.2 Configure `.env`
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=hrportal
DB_USERNAME=hruser
DB_PASSWORD=hrpassword
LOCALE=pl
APP_LOCALE=pl
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

#### `employees` (Extended)
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| first_name | VARCHAR(255) | (Imię) |
| last_name | VARCHAR(255) | (Nazwisko) |
| pesel | VARCHAR(11) | optional unique — 11-digit Polish PESEL validation |
| email | VARCHAR(255) | optional unique |
| position | VARCHAR(255) | (Stanowisko) |
| department | VARCHAR(255) | (Dział) |
| phone | VARCHAR(50) | optional |
| hire_date | DATE | optional |
| salary | DECIMAL(10,2) | optional |
| status | ENUM('active','inactive','terminated') | default 'active' |
| address | TEXT | optional (Adres: street, city, postalCode) |
| company_id | BIGINT UNSIGNED FK→companies | nullable (relacja z firmą) |
| created_by | BIGINT UNSIGNED FK→users | nullable |
| updated_by | BIGINT UNSIGNED FK→users | nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `companies` (New)
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| name | VARCHAR(255) | (Nazwa firmy) |
| nip | VARCHAR(10) | unique — 10-digit Polish NIP validation |
| street | VARCHAR(255) | (Ulica) |
| city | VARCHAR(100) | (Miasto) |
| postal_code | VARCHAR(10) | (Kod pocztowy) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `exposure_categories` (New)
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| code | VARCHAR(10) | I–V (kategoria) |
| name | VARCHAR(255) | (Nazwa kategorii) |
| sort_order | INT | default 0 (kolejność) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `exposure_factors` (New)
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| exposure_category_id | BIGINT UNSIGNED FK→exposure_categories | |
| name | VARCHAR(255) | (Nazwa czynnika) |
| description | TEXT | optional (Opis) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `referrals` (New)
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| employee_id | BIGINT UNSIGNED FK→employees | |
| examination_type | ENUM('wstępne','okresowe','kontrolne') | (Typ badania) |
| job_position | VARCHAR(255) | (Stanowisko pracy) |
| job_description | TEXT | (Opis warunków pracy) |
| issue_place | VARCHAR(255) | (Miejsce wystawienia) |
| issue_date | DATE | (Data wystawienia) |
| created_by | BIGINT UNSIGNED FK→users | nullable (Ustalone automatycznie) |
| pdf_path | VARCHAR(500) | optional (Ścieżka do PDF) |
| pdf_generated_at | TIMESTAMP | nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

#### `referral_exposure_factors` (New)
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT UNSIGNED PK | auto-increment |
| referral_id | BIGINT UNSIGNED FK→referrals | |
| exposure_factor_id | BIGINT UNSIGNED FK→exposure_factors | |
| exposure_details | TEXT | optional (Wielkość narażenia / wyniki pomiarów) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### 3.2 Migrations Summary
- `roles` table
- `users` table
- `user_roles` pivot table
- `companies` table (new)
- `employees` table (extended: pesel, address, company_id)
- `exposure_categories` table (new)
- `exposure_factors` table (new)
- `referrals` table (new)
- `referral_exposure_factors` table (new)

### 3.3 Seeders
- **AdminRoleSeeder**: Create "Admin" role
- **UserRoleSeeder**: Create "Użytkownik" role
- **UserSeeder**: Create default admin user with email `admin@hrportal.local`, password `password`
- **CompanySeeder**: (Optional) seed sample companies
- **ExposureCategorySeeder**: Create exactly 5 `ExposureCategory` records matching Annex 3 of *Rozporządzenie Ministra Zdrowia i Opieki Społecznej z dnia 30 maja 1996 r.*:
  - Category I: Czynniki fizyczne (Hałas, Hałas ultradźwiękowy, Wibracje ogólne/miejscowe, Mikroklimat, Promieniowanie, Promieniowanie laserowe, Promieniowanie jonizujące, Pole elektromagnetyczne, Praca przy monitorach >4h)
  - Category II: Pyły (azbest, krzemionka, drewna, rud metali, węgla, mączne, bawełny, zwierzęce/roślinne)
  - Category III: Czynniki chemiczne (toksykczne, drażniące, uczulające, rakotwórcze/mutagenne, szkodliwe na rozrodczość, rozpuszczalniki, metale, pestycydy, tlenku węgla/azotu)
  - Category IV: Czynniki biologiczne (Wirusy, Bakterie, Grzyby, Pasożyty, Materiał biologiczny ludzki, Mikroorganizmy, Kontakt ze zwierzętami)
  - Category V: Inne czynniki niebezpieczne (Praca na wysokości, Obsługa maszyn, Wymuszona pozycja, Obciążenie statyczne/dynamiczne, Praca nocna/zmianowa, Obciążenie psychiczne, Praca przy urządzeniach pod napięciem, Widoczność, Ryzyko poślizgnięcia)
- **ExposureFactorSeeder**: Create factors for all 5 categories
- **DatabaseSeeder**: Run all seeders

---

## Step 4 — Authentication

### 4.1 Auth System (Session-based)
- Use Laravel's built-in authentication scaffolding
- Login: email + password
- Register: optional (or only admins can create users)
- Use `Auth` facade
- **All auth-related UI labels in Polish** ("Zaloguj się", "E-mail", "Hasło", "Wyloguj")

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

## Step 7 — Employees Panel (Extended)

### 7.1 Employees Controller
- `EmployeeController@index` — list all employees (search + paginate, filter by company)
- `EmployeeController@show` — view employee details
- `EmployeeController@create` — form for new employee
- `EmployeeController@store` — validate (including PESEL: 11-digit Polish PESEL validation) and save employee
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

## Step 8 — Companies (New)

### 8.1 Companies Controller
- `CompanyController@index` — list all companies (paginated, search by name/NIP)
- `CompanyController@show` — view company details
- `CompanyController@create` — form for new company
- `CompanyController@store` — validate (NIP: 10-digit Polish NIP validation) and save
- `CompanyController@edit` — edit existing company
- `CompanyController@update` — update company
- `CompanyController@destroy` — delete company (check employees first)

### 8.2 Company Routes (Authenticated)
```
GET   /panel/companies          → CompanyController@index
GET   /panel/companies/create   → CompanyController@create
POST  /panel/companies          → CompanyController@store
GET   /panel/companies/{company}    → CompanyController@show
GET   /panel/companies/{company}/edit → CompanyController@edit
PATCH /panel/companies/{company}    → CompanyController@update
DELETE /panel/companies/{company}   → CompanyController@destroy
```

### 8.3 Company Form Fields (in Polish)
- Nazwa firmy (Name)
- NIP (10-digit validation)
- Ulica (Street)
- Miasto (City)
- Kod pocztowy (Postal code)

---

## Step 9 — Exposure Factors (Categories + Factors) (New)

### 9.1 Exposure Category Controller
- `ExposureCategoryController@index` — list all categories (read-only, ordered by `sortOrder`)
- (Categories seeded from ExposureCategorySeeder — not editable/removable via UI)

### 9.2 Exposure Factor Controller
- `ExposureFactorController@index` — list factors filtered by category
- `ExposureFactorController@create` — form for new factor (select category, name, description)
- `ExposureFactorController@store` — save factor
- `ExposureFactorController@destroy` — delete factor (check referrals first, return 422 if referenced)

### 9.3 Exposure Factor Routes (Authenticated)
```
GET   /panel/exposure-factors       → ExposureFactorController@index
GET   /panel/exposure-factors/create → ExposureFactorController@create
POST  /panel/exposure-factors       → ExposureFactorController@store
DELETE /panel/exposure-factors/{factor} → ExposureFactorController@destroy
```

### 9.4 Exposure Categories (Seeded)
- **Category I: Czynniki fizyczne** — Hałas, Hałas ultradźwiękowy, Wibracje ogólne/miejscowe, Mikroklimat gorący/zimny, Promieniowanie optyczne, Promieniowanie laserowe, Promieniowanie jonizujące, Pole elektromagnetyczne, Praca przy monitorach >4h
- **Category II: Pyły** — Azbest, Krzemionka, Drewna, Rudy metali, Węgla, Mączne, Bawełny, Zwierzęce/roślinne
- **Category III: Czynniki chemiczne** — Toksykczne, Drażniące, Uczulające, Rakotwórcze/mutagenne, Szkodliwe na rozrodczość, Rozpuszczalniki, Metale, Pestycydy, Tlenku węgla/azotu
- **Category IV: Czynniki biologiczne** — Wirusy, Bakterie, Grzyby, Pasożyty, Materiał biologiczny ludzki, Mikroorganizmy rolnicze/leśne, Kontakt ze zwierzętami
- **Category V: Inne czynniki niebezpieczne** — Praca na wysokości, Obsługa maszyn, Wymuszona pozycja, Obciążenie statyczne/dynamiczne, Praca nocna/zmianowa, Obciążenie psychiczne, Praca przy urządzeniach pod napięciem, Widoczność, Ryzyko poślizgnięcia

---

## Step 10 — Medical Examination Referrals (New)

### 10.1 Referral Controller
- `ReferralController@index` — list all referrals (paginated, ordered by `issueDate` desc, show PDF status)
- `ReferralController@show` — view referral details
- `ReferralController@create` — form for new referral (employee select, type wstępne/okresowe/kontrolne, job position, job description, issue place, issue date, exposure factors with checkboxes and optional exposure details per factor)
- `ReferralController@store` — validate (enum `examinationType`, employee must exist) and save `Referral` + `ReferralExposureFactor` rows in one transaction
- `ReferralController@destroy` — delete referral (cascade delete `ReferralExposureFactor` rows)
- `ReferralController@generatePdf` — generate PDF using `dompdf/dompdf`, write PDF to storage, set `pdf_path` and `pdf_generated_at`
- `ReferralController@downloadPdf` — stream/download PDF file (404 if no PDF generated)

### 10.2 Referral Routes (Authenticated)
```
GET     /panel/referrals            → ReferralController@index
GET     /panel/referrals/create     → ReferralController@create
POST    /panel/referrals            → ReferralController@store
GET     /panel/referrals/{referral}     → ReferralController@show
GET     /panel/referrals/{referral}/pdf → ReferralController@downloadPdf
POST    /panel/referrals/{referral}/generate-pdf → ReferralController@generatePdf
DELETE  /panel/referrals/{referral}   → ReferralController@destroy
```

### 10.3 PDF Generation
- Use `dompdf/dompdf` package
- Generate "Skierowanie na badania lekarskie" document with:
  - Nagłówek: dane firmy (nazwa, adres), miejsce i data wystawienia
  - Tytuł: "SKIEROWANIE NA BADANIA LEKARSKIE (wstępne/okresowe/kontrolne)"
  - Dane pracownika: imię, nazwisko, PESEL, adres
  - Stanowisko pracy, opis warunków pracy
  - Sekcje I–V z wybranymi czynnikami narażenia i detalami ekspozycji
  - Liczba czynników łącznie
  - Podpis linia

### 10.4 Referral Form Fields (in Polish)
- Pracownik (select/search)
- Typ badania: wstępne / okresowe / kontrolne
- Stanowisko pracy
- Opis warunków pracy
- Miejsce wystawienia
- Data wystawienia (default: today)
- Czynniki narażenia: checkboxes per category (I–V), each with "Wielkość narażenia / wyniki pomiarów" text input

---

## Step 11 — Dashboard (New)

### 11.1 Dashboard Controller
- `DashboardController@index` — show summary counts:
  - Total Companies (Ilość firm)
  - Total Employees (Ilość pracowników)
  - Total Referrals (Ilość sk疑难owań)
  - Referrals with PDF generated (Skierowania wygenerowane)

### 11.2 Dashboard Route (Authenticated)
```
GET /panel/dashboard → DashboardController@index
```

### 11.3 Dashboard View (in Polish)
- "Dashboard" — Strona główna
- "Ilość firm" — Companies count
- "Ilość pracowników" — Employees count
- "Ilość skierowań" — Referrals count
- "Wygenerowane PDF" — PDF generated count

---

## Step 12 — Frontend (Bootstrap 5 + Polish Translations)

### 12.1 Layout
- `resources/views/layouts/app.blade.php` — master layout with:
  - Bootstrap 5 CDN (or npm)
  - Navbar: "Portal HR", login/register links, logged-in user dropdown ("Wyloguj")
  - Sidebar (for logged-in users): "Dashboard", "Firmy" (new), "Pracownicy", "Użytkownicy (Admin only)", "Czynniki narażeń" (new), "Skierowania" (new), "Roles (Admin only)"
  - Flash messages area — Polish messages

### 12.2 Auth Views
- `resources/views/auth/login.blade.php` — email + password form (Polish: "Zaloguj się", "E-mail", "Hasło", "Nieprawidłowy e-mail lub hasło")
- `resources/views/auth/register.blade.php` — name + email + password (optional, Polish labels)

### 12.3 Panel Views — Users
- `resources/views/panel/users/index.blade.php` — table with name, email, roles, actions (Polish labels: "Imię", "E-mail", "Role", "Akcje")
- `resources/views/panel/users/create.blade.php` — form (Polish labels: "Dodaj użytkownika", "Nazwa", "E-mail", "Hasło", "Role", "Zapisz", "Anuluj")
- `resources/views/panel/users/edit.blade.php` — form (Polish labels: "Edytuj użytkownika")
- `resources/views/panel/users/show.blade.php` — details + assigned roles (Polish labels)

### 12.4 Panel Views — Roles
- `resources/views/panel/roles/index.blade.php` — list of roles (Polish labels: "Role", "Nazwa roli", "Użytkownicy")
- `resources/views/panel/roles/show.blade.php` — role + assigned users (Polish labels)

### 12.5 Panel Views — Companies (New)
- `resources/views/panel/companies/index.blade.php` — table with name, NIP, address, actions (Polish labels: "Firmy", "Nazwa", "NIP", "Adres", "Akcje")
- `resources/views/panel/companies/create.blade.php` — form (Polish labels: "Dodaj firmę", "Nazwa firmy", "NIP", "Ulica", "Miasto", "Kod pocztowy", "Zapisz", "Anuluj")
- `resources/views/panel/companies/edit.blade.php` — form (Polish labels: "Edytuj firmę")
- `resources/views/panel/companies/show.blade.php` — details (Polish labels)

### 12.6 Panel Views — Employees (Extended)
- `resources/views/panel/employees/index.blade.php` — table with search, name, phone, email, address, company name, status, actions (Polish labels: "Pracownicy", "Imię", "Nazwisko", "PESEL", "Stanowisko", "Dział", "Telefon", "E-mail", "Adres", "Firma", "Status", "Akcje")
- `resources/views/panel/employees/create.blade.php` — form (Polish labels: "Dodaj pracownika", "Imię", "Nazwisko", "PESEL (11 cyfr)", "Telefon", "E-mail", "Adres", "Firma (dropdown)", "Stanowisko", "Dział", "Data zatrudnienia", "Pensja", "Status", "Zapisz", "Anuluj")
- `resources/views/panel/employees/edit.blade.php` — form (Polish labels: "Edytuj pracownika")
- `resources/views/panel/employees/show.blade.php` — employee details (Polish labels)

### 12.7 Panel Views — Exposure Factors (New)
- `resources/views/panel/exposure-factors/index.blade.php` — 5 categories (I–V) as sections/cards listing factors (Polish labels: "Czynniki narażeń", "Kategoria I: Czynniki fizyczne", etc.)
- `resources/views/panel/exposure-factors/create.blade.php` — form per category (Polish labels: "Dodaj czynnik", "Kategoria", "Nazwa czynnika", "Opis", "Zapisz", "Anuluj")

### 12.8 Panel Views — Referrals (New)
- `resources/views/panel/referrals/index.blade.php` — creation form at top, table listing (Pracownik, Typ badania, Stanowisko, Data wystawienia, Status PDF: Wygenerowano/Nie wygenerowano), "Generuj PDF" button per row → "Pobierz PDF" link (Polish labels: "Skierowania", "Pracownik", "Typ badania", "Stanowisko", "Data wystawienia", "Status PDF", "Generuj PDF", "Pobierz PDF")
- `resources/views/panel/referrals/create.blade.php` — form (Polish labels: "Skierowanie na badania lekarskie", "Pracownik", "Typ badania: wstępne/okresowe/kontrolne", "Stanowisko pracy", "Opis warunków pracy", "Miejsce wystawienia", "Data wystawienia", "Czynniki narażenia — Kategoria I–V", "Wielkość narażenia / wyniki pomiarów", "Zapisz", "Anuluj")

### 12.9 Dashboard View (New)
- `resources/views/panel/dashboard.blade.php` — summary counts (Polish labels: "Dashboard", "Ilość firm", "Ilość pracowników", "Ilość skierowań", "Wygenerowane PDF")

---

## Step 13 — Routing & Middleware Summary

### Routes (`routes/web.php`)
```php
// Public
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Authenticated
Route::middleware(['auth'])->group(function () {
    // Dashboard (new)
    Route::get('/panel/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employees (extended)
    Route::get('/panel/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/panel/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/panel/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/panel/employees/{employee}', [Employee::class, 'show'])->name('employees.show');
    Route::get('/panel/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::patch('/panel/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/panel/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Companies (new)
    Route::get('/panel/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/panel/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/panel/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/panel/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/panel/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::patch('/panel/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/panel/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    // Exposure Factors (new)
    Route::get('/panel/exposure-factors', [ExposureFactorController::class, 'index'])->name('exposure-factors.index');
    Route::get('/panel/exposure-factors/create', [ExposureFactorController::class, 'create'])->name('exposure-factors.create');
    Route::post('/panel/exposure-factors', [ExposureFactorController::class, 'store'])->name('exposure-factors.store');
    Route::delete('/panel/exposure-factors/{factor}', [ExposureFactorController::class, 'destroy'])->name('exposure-factors.destroy');

    // Referrals (new)
    Route::get('/panel/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::get('/panel/referrals/create', [ReferralController::class, 'create'])->name('referrals.create');
    Route::post('/panel/referrals', [ReferralController::class, 'store'])->name('referrals.store');
    Route::get('/panel/referrals/{referral}', [ReferralController::class, 'show'])->name('referrals.show');
    Route::get('/panel/referrals/{referral}/pdf', [ReferralController::class, 'downloadPdf'])->name('referrals.pdf');
    Route::post('/panel/referrals/{referral}/generate-pdf', [ReferralController::class, 'generatePdf'])->name('referrals.generate-pdf');
    Route::delete('/panel/referrals/{referral}', [ReferralController::class, 'destroy'])->name('referrals.destroy');

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

## Step 14 — Validation Rules (Polish)

### 14.1 Polish NIP Validation
- 10-digit Polish NIP number validation for `Company::nip`
- Return Polish error message on violation

### 14.2 Polish PESEL Validation
- 11-digit Polish PESEL number validation for `Employee::pesel`
- Return Polish error message on violation

### 14.3 Examination Type Enum
- `examination_type` must be one of: `wstępne`, `okresowe`, `kontrolne`
- Return Polish error message on violation

---

## Step 15 — Translation Files

### 15.1 Polish Language (`resources/lang/pl/`)
- All views must be translated to Polish, including:
  - Navigation: "Portal HR", "Dashboard", "Firmy", "Pracownicy", "Skierowania", "Użytkownicy", "Czynniki narażeń", "Role", "Zaloguj się", "Wyloguj"
  - Buttons: "Zapisz", "Edytuj", "Usuń", "Dodaj", "Anuluj", "Szukaj", "Generuj PDF", "Pobierz PDF"
  - Labels: "Imię", "Nazwisko", "PESEL", "Stanowisko", "Dział", "Telefon", "E-mail", "Adres", "Firma", "NIP", "Status", "Typ badania", "Stanowisko pracy", "Opis warunków pracy", "Miejsce wystawienia", "Data wystawienia", "Kategoria", "Nazwa czynnika", "Wielkość narażenia"
  - Flash messages in Polish
  - Error messages in Polish ("Nieprawidłowy e-mail lub hasło", "Błąd walidacji", etc.)

---

## Implementation Order

| Step | Task | Status |
|------|------|--------|
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
| 14 | **New: Companies** — Create Company model + Controller + views (seeders for companies) | [ ] |
| 15 | **New: Extended Employees** — Update Employee migration (add pesel, address, company_id), update EmployeeController + views | [ ] |
| 16 | **New: Exposure Factors** — Create ExposureCategory + ExposureFactor models + seeders + Controller + views | [ ] |
| 17 | **New: Referrals** — Create Referral + ReferralExposureFactor models + Controller (incl. PDF generation) + views | [ ] |
| 18 | **New: Dashboard** — Create DashboardController + views | [ ] |
| 19 | **New: Polish Translations** — Create `resources/lang/pl/` files for all UI text | [ ] |
| 20 | Final integration testing (docker compose up, migrate, seed all, verify) | [ ] |
