# Laravel Basics — From a Symfony Developer's Perspective

This guide walks through Laravel concepts using **HR Portal**, your actual application at `/home/jakub/workspace/LaravelPi`. I'll constantly reference your code so you can see theory in action.

---

## 1. Project Structure — The Big Picture

Laravel has a very fixed directory structure. Here's how your app maps:

```
LaravelPi/
├── app/                      # Your business logic (like src/ in Symfony)
│   ├── Http/
│   │   ├── Controllers/      # Controllers (like Symfony's src/Controller/)
│   │   └── Middleware/       # HTTP middleware
│   └── Providers/            # Service providers (think: extension registration)
├── bootstrap/                # Framework bootstrapping (no Symfony equivalent)
│   ├── app.php               # Register routes, middleware, exceptions
│   └── providers.php         # List of service providers
├── config/                   # Configuration files (like config/*.yaml)
├── database/
│   ├── migrations/           # Database schema versioning (like Doctrine migrations)
│   ├── seeders/              # Seed initial data (like DoctrineFixturesBundle)
│   └── factories/            # Test data factories (like Fixtures, but for DB)
├── public/
│   └── index.php             # Single entry point (same as Symfony's public/index.php)
├── resources/
│   ├── views/                # Blade templates (Twig's counterpart)
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php               # Web routes (like routing but in PHP files)
│   └── console.php           # Artisan CLI commands
├── storage/                  # Logs, compiled views, cache, uploads
└── tests/                    # PHPUnit tests
```

### Key Structural Differences from Symfony

| Symfony | Laravel | Notes |
|---------|---------|-------|
| `src/Controller/` | `app/Http/Controllers/` | Same concept |
| `src/Entity/` + Doctrine ORM | `app/Models/` (Eloquent ORM) | Eloquent uses PHP magic; no YAML/XML mapping |
| `config/packages/` | `config/*.php` | Laravel configs are plain PHP arrays |
| `config/bundles.php` | `bootstrap/providers.php` | Registering service providers |
| Routing via `src/Controller/*.php` (annotations/attributes) | `routes/web.php` | All routes in one file — the big difference! |
| Twig templates | Blade templates (`.blade.php`) | Blade uses `{{ }}` like Twig, but has extra directives |
| `Kernel.php` | `bootstrap/app.php` (modern Laravel) | Single bootstrap file replacing Http/Kernel + Console/Kernel |

---

## 2. The Bootstrapping Process

### Entry Point: `public/index.php`

```php
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
```

This is Laravel 11+'s "minimal" bootstrap. In older Laravel, this was spread across `Http\Kernel.php` and `Console\Kernel.php`. Now everything is configured in one place.

### Application Configuration: `bootstrap/app.php`

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
```

**Symfony equivalent:** Your `config/routes.yaml` + `Kernel::registerContainerConfiguration()` + `Kernel::registerBundles()`. Laravel groups all of this into 3 fluent method calls.

### Service Providers: `bootstrap/providers.php`

```php
return [
    AppServiceProvider::class,
];
```

A **Service Provider** is Laravel's way of registering services (DI bindings, event listeners, etc.) into the container. In Symfony, you do this in `src/Kernel::registerBundles()` + `config/services.yaml`. In Laravel, each provider can have a `register()` method (for binding things into the container) and a `boot()` method (for using things that are already bound).

Your `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }
    public function boot(): void { }
}
```

It's a no-op right now — but this is where you'd extend validation rules, register view composers, or bind custom interfaces.

---

## 3. Routing — The Big Change from Symfony

In Symfony, routing is either via attributes on controllers or in `config/routes.yaml`. In Laravel, all routes live in `routes/web.php`.

### Your Routes (`routes/web.php`)

```php
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/panel/employees');
    }
    return view('auth.login');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Authenticated routes (resourceful CRUD)
Route::middleware(['auth'])->group(function () {
    Route::get('/panel/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/panel/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/panel/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/panel/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/panel/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::patch('/panel/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/panel/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});
```

### Key Observations

| Laravel | Symfony | Notes |
|---------|---------|-------|
| `Route::get('/login', [Ctrl::class, 'method'])` | `#[Route('/login', methods: ['GET'])]` on the method | Laravel uses controllers as **class name + method name** instead of PHP attributes on the method |
| `->name('employees.index')` | No explicit naming (URL is the route name) | Laravel requires explicit route names via `->name()` |
| `Route::middleware(['auth'])->group(...)` | `#[IsGranted('ROLE_USER')]` or firewall rules | Laravel's middleware is **reusable filters** applied to routes |
| `Route::middleware(['role:admin'])->group(...)` | Custom Voter or `@IsGranted('ADMIN')` | Laravel lets you pass parameters to middleware via `:` |
| `auth()->check()` / `auth()->user()` | `$this->getUser()` from `AbstractController` | Laravel has **helper functions** (`auth()`, `route()`, `view()`) everywhere |

### Resourceful Routing Shortcut

Instead of 7 routes for CRUD, Laravel 11+ lets you write:

```php
Route::resource('panel/employees', EmployeeController::class)->middleware('auth');
```

This auto-generates all 7 routes. Your app explicitly lists them (useful when you need custom route names or non-standard HTTP methods), but `Route::resource()` is the common idiom.

### Route Model Binding

Look at your controller method:

```php
public function show(Employee $employee): View
```

Laravel **automatically resolves** the `{employee}` URL parameter to an `Employee` model by the `id` column. This is like Symfony's `#[ParamName('id')]` but magical — no explicit annotation needed. If no record is found, Laravel throws a 404 automatically.

---

## 4. Controllers — From Symfony to Laravel

### Base Controller

```php
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
```

In Symfony, your controllers extend `AbstractController` which gives you `$this->render()`, `$this->redirect()`, `$this->isCsrfTokenValid()`, etc.

In Laravel, the base `Controller` class uses two **traits**:

- **`AuthorizesRequests`** — Gives you `$this->authorize()` for policy-based authorization
- **`ValidatesRequests`** — Gives you `$request->validate()` for inline validation

### Your EmployeeController — A Full Example

```php
public function index(Request $request): View
{
    $query = Employee::orderBy('last_name');

    // Search
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
    }

    // ... filters ...

    $employees = $query->paginate(15)->withQueryString();
    return view('panel.employees.index', compact('employees', 'departments'));
}
```

### Validation (`$request->validate()`)

```php
$validated = $request->validate([
    'first_name' => 'required|string|max:255',
    'email'      => 'nullable|email|max:255|unique:employees,email',
    'status'     => 'required|in:active,inactive,terminated',
    'salary'     => 'nullable|numeric|min:0',
]);
```

This is Laravel's **string-based rule syntax**. Compare to Symfony's validators:

| Laravel rule string | Symfony Validator | Notes |
|---------------------|-------------------|-------|
| `required` | `NotBlank` + `NotNull` | |
| `nullable` | `Nullabe` wrapper | Can be null OR pass the other rules |
| `email` | `Email` | |
| `max:255` | `Length(maxLength: 255)` | |
| `unique:table,column` | `UniqueEntity` (Doctrine level) | Laravel validates at the request level, not entity level |
| `in:active,inactive,terminated` | `Choice` | |
| `numeric` | `Range` or `NotNull` | |
| `min:0` | `PositiveOrZero` or `Range` | |
| `date` | `DateTime` | |
| `array` + `*` sub-rule | `Valid` + collection validator | `rules.*` applies to each array element |
| `confirmed` | Custom logic for `password` + `password_confirmation` | Laravel auto-looks for `{field}_confirmation` |

**Important difference:** In Symfony, validation is usually tied to DTOs or Entity constraints (Doctrine ORM). In Laravel, validation happens **purely at the controller level** — Eloquent doesn't validate. You call `$request->validate()` explicitly in each controller method.

### Mass Assignment (`$fillable`)

In Eloquent, you specify which attributes can be mass-assigned:

```php
protected $fillable = [
    'first_name', 'last_name', 'email', 'position', ...
];
```

```php
$employee = Employee::create($validated);  // Safe — only $fillable fields
```

This protects against **mass assignment vulnerabilities**. In Symfony/Doctrine, you handle this by creating DTOs and mapping to entities manually. In Laravel, `$fillable` is the safety net (opposite of Symfony's "trust the entity").

### Returning Views

```php
return view('panel.employees.index', compact('employees', 'departments'));
```

- `view('path.to.view')` — loads a Blade template
- `compact('key1', 'key2')` — PHP helper that creates `['key1' => $key1, 'key2' => $key2]`
- In Symfony: `$this->render('path/to/view.html.twig', ['employees' => $employees])`

---

## 5. Eloquent ORM — Doctrine's Challenger

Eloquent is Laravel's Object-Relational Mapper. Where Doctrine uses annotations/attributes on PHP classes + XML/YAML mapping files, Eloquent **lives entirely in the PHP model class**.

### Your User Model

```php
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function createdEmployees(): HasMany
    {
        return $this->hasMany(Employee::class, 'created_by');
    }
}
```

### Key Concepts

| Symfony/Doctrine | Laravel/Eloquent | Notes |
|------------------|------------------|-------|
| `#[Entity]` + `#[Column]` annotations | No annotations — properties map by name | Eloquent's `name` field maps to `name` column automatically |
| `#[OneToMany]`, `#[ManyToOne]` | `hasMany()`, `belongsTo()` PHP methods | Relations are **PHP methods**, not annotations |
| `EntityManager` | `Model::all()`, `Model::find()` | Eloquent models **are** the repository |
| DQL queries | Query Builder / Eloquent chains | `$users->where('active', true)->orderBy('name')->get()` |
| `#[ORM\OneToMany(mappedBy: ...)]` | `$this->hasMany(...)` with `foreignKey` | No inverse mapping annotations; Laravel infers from foreign key |

### Your Employee Model

```php
class Employee extends Model
{
    protected $fillable = ['first_name', 'last_name', ...];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',      // Auto-converts to Carbon instance
            'salary' => 'decimal:2',    // Auto-converts to float with 2 decimals
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor (auto-attribute)
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
```

### Magic Accessors (Getters/Setters)

```php
$employee->full_name  // Automatically calls getFullNameAttribute()
```

In Symfony/Doctrine, you'd add a `getFullName(): string` method. In Laravel, the `get{Attribute}Attribute()` convention is "magic" — you don't call it directly, you just access the attribute name as if it were a property.

### Casting

```php
protected function casts(): array
{
    return [
        'hire_date' => 'date',       // Carbon instance
        'salary' => 'decimal:2',     // Float rounded to 2 decimals
    ];
}
```

Symfony/Doctrine handles types via column definitions. Laravel uses the `casts()` method to transform values when reading/writing.

### Relationships (from the Migration perspective)

**`belongsToMany` (Many-to-Many)**

```php
// User model
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class, 'user_role');
}

// Role model
public function users(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'user_role');
}
```

The pivot table is `user_role` (alphabetical order of the two model names, singular). You can manually manage it:

```php
// In UserController::store()
$user->roles()->attach($validated['roles']);  // Insert rows into user_role

// In UserController::update()
$user->roles()->sync($validated['roles']);    // Delete old + insert new = full sync
```

Compare: in Symfony/Doctrine, `@ORM\ManyToMany` + `$roles->addRole($role)` handles this at the ORM level. In Laravel, you call `attach()` and `sync()` explicitly.

---

## 6. Database Migrations

### How They Work

```bash
php artisan migrate          # Run pending migrations
php artisan migrate:rollback # Undo the last batch
php artisan migrate:status   # See what's been applied
```

### Your Migrations

Each migration is a class with `up()` and `down()` methods:

```php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();                                    // BIGINT UNSIGNED, auto-increment (like Doctrine's @ORM\GeneratedValue)
            $table->string('first_name');                     // VARCHAR
            $table->string('email')->nullable()->unique();    // Nullable + unique index
            $table->decimal('salary', 10, 2)->nullable();     // DECIMAL(10,2)
            $table->enum('status', ['active','inactive','terminated'])->default('active');
            $table->foreignId('created_by')                  // BIGINT UNSIGNED
                     ->nullable()->constrained('users')       // FOREIGN KEY -> users(id)
                     ->nullOnDelete();
            $table->timestamps();                             // created_at + updated_at (both TIMESTAMP)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
```

### Key Blueprint Methods

| Blueprint Method | Doctrine Equivalent | SQL Type |
|------------------|---------------------|----------|
| `$table->id()` | `#[Id] #[GeneratedValue]` | `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
| `$table->string('col')` | `#[Column(type: 'string')]` | `VARCHAR(255)` |
| `$table->text('col')` | `#[Column(type: 'text')]` | `TEXT` |
| `$table->integer('col')` | `#[Column(type: 'integer')]` | `INT` |
| `$table->boolean('col')` | `#[Column(type: 'boolean')]` | `BOOLEAN` |
| `$table->decimal('col', 10, 2)` | `#[Column(type: 'decimal')]` | `DECIMAL(10,2)` |
| `$table->enum('col', [...])` | Custom DBAL Type | `ENUM(...)` |
| `$table->date('col')` | `#[Column(type: 'date')]` | `DATE` |
| `$table->foreignId('col')->constrained('table')` | `#[ForeignKey]` or `#[ManyToOne]` | `FOREIGN KEY` |
| `$table->foreignId('col')->constrained()->nullOnDelete()` | `#[ORM\OnDelete('NULL')]` | `ON DELETE SET NULL` |
| `$table->timestamps()` | `#[Column(type: 'datetime_immutable')]` x2 | `created_at`, `updated_at` |

### Important: `$table->timestamps()`

This creates **both** `created_at` and `updated_at` columns. Eloquent will automatically set them on `create()` and `update()`. In Symfony/Doctrine, you'd add `#[ORM\Column(type: 'datetime_immutable')]` to each property.

### Seeders

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminRoleSeeder::class,   // Runs AdminRoleSeeder first
            AdminUserSeeder::class,   // Then AdminUserSeeder
        ]);
    }
}
```

```php
// AdminRoleSeeder
Role::firstOrCreate(
    ['name' => 'admin'],
    ['description' => 'Administrator with full access...']
);
```

`firstOrCreate()` = **find by first array, if not found, create with second array**. Like Symfony's `findOrCreate` pattern, but built directly into the model.

Run with:

```bash
php artisan db:seed
```

---

## 7. Middleware — Request Filtering

### What Middleware Is

Middleware sits **between** the HTTP request and your controller. It can:
- Authenticate the user
- Log requests
- Apply CORS headers
- Check permissions (like your `RoleMiddleware`)
- Modify the response

### Your Role Middleware

```php
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (Auth::user()->isAdmin()) {
            return $next($request);  // Admin passes through
        }

        foreach ($roles as $role) {
            if (Auth::user()->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized. You do not have the required role.');
    }
}
```

### Registering It

```php
// In bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

### Using It in Routes

```php
Route::middleware(['role:admin'])->group(function () {
    // Only users with 'admin' role reach here
});
```

The `role:admin` syntax passes `"admin"` as the `$roles` parameter. You could also use `role:admin,manager`.

### Comparison to Symfony

| Laravel | Symfony | Notes |
|---------|---------|-------|
| `middleware('auth')` per route | Firewall + Access Control List | Laravel middleware is **explicit per route** |
| `abort(403)` | Custom Voter | Laravel's `abort()` throws an exception that maps to an HTTP status |
| `$middleware` global list (in Kernel) | Kernel's `handle()` | Laravel 11's minimal kernel still allows global middleware |

---

## 8. Authentication — Built-In

Laravel ships with authentication helpers. Your app uses a **custom, manual approach** (not Laravel Breeze/Jetstream):

```php
// LoginController::store()
$credentials = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
]);

if (Auth::attempt($credentials, $request->boolean('remember'))) {
    $request->session()->regenerate();  // Prevent session fixation
    return redirect()->intended('/panel/employees');
}
```

### Key Auth Helpers

| Function | What It Does | Symfony Equivalent |
|----------|-------------|-------------------|
| `Auth::attempt($credentials)` | Checks email+password, logs in if valid | `AuthenticationProviderManager` + `LoginFormAuthenticator` |
| `auth()->check()` | Is user logged in? | `$this->getUser() !== null` |
| `auth()->user()` | Get current User model | `$this->getUser()` |
| `session()->regenerate()` | New session ID after login | `LoginLinkHandler` does this automatically |
| `session()->invalidate()` | Destroy session (logout) | `LogoutHandler` does this |
| `session()->regenerateToken()` | CSRF token regeneration | `CsrfTokenManager::refreshToken()` |
| `env('DB_HOST')` | Read .env variables | `%env(DATABASE_URL)%` or `#[%env(DATABASE_HOST)%]%` in YAML |

### Guards and Providers (`config/auth.php`)

```php
'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],
],
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => User::class,
    ],
],
```

This tells Laravel:
- The `web` guard uses **session** authentication
- The `users` provider queries via **Eloquent ORM**
- Using the `App\Models\User` model

This is like Symfony's `security.firewalls.main` + `security.providers` configuration.

---

## 9. Blade Templates — Twig's Equivalent

Blade is Laravel's templating engine. It compiles to plain PHP at runtime (views are cached in `storage/framework/views/`).

### Your Layout (`resources/views/layouts/app.blade.php`)

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', config('app.name', 'HR Portal'))</title>
    @stack('styles')
</head>
<body>
    @auth
        <nav>...</nav>
        @yield('content')
    @else
        @yield('content')
    @endauth
    @stack('scripts')
</body>
</html>
```

### Blade Directives — Quick Reference

| Blade Directive | Twig Equivalent | What It Does |
|-----------------|-----------------|-------------|
| `{{ $variable }}` | `{{ variable }}` | Echo escaped output (XSS-safe by default) |
| `{!! $html !!}` | `{% raw %}{{ variable }}{% endraw %}` | Echo **unescaped** output (dangerous!) |
| `@if(...)` / `@else` / `@endif` | `{% if %}` | Control structures |
| `@foreach($items as $item)` | `{% for item in items %}` | Loops |
| `@extends('layout')` | `{% extends "layout" %}` | Inherit from a parent template |
| `@section('title') ... @endsection` | `{% block title %}` | Override sections in parent |
| `@yield('content')` | `{% block content %}` | Define a placeholder section |
| `@stack('scripts')` | `{% block scripts %}` | Push to a stack, render at parent |
| `@auth` / `@endauth` | `{% if app.user %}` | Check if logged in |
| `@error('field')` | `{% for err in errors('field') %}` | Show field validation errors |
| `@csrf` | `{% csrf_token %}` + hidden field | CSRF protection token |
| `@method('DELETE')` | `{% form_type_render(form) %}` | Override HTTP method (POST → DELETE) |
| `@route('name')` | `path('name')` | Generate URL from route name |
| `@session('success')` | `app.session.flash('success')` | Read flash message |

### Your Layout Explained

```blade
@extends('layouts.app')           // Extends resources/views/layouts/app.blade.php

@section('title', 'Employees')    // Sets the 'title' section

@section('content')                // Fills the 'content' section
    <!-- HTML table here -->
    {{ $employees->links() }}      // Pagination links (rendered as Bootstrap HTML)
@endif
@endsection
```

### How Blade Views Are Referenced

```php
return view('panel.employees.index');
//      ^^^^^^^^^^^^^^^^^^^^^^^^^^^
//      maps to resources/views/panel/employees/index.blade.php
```

Use dots (`.`) as directory separators. This is like Symfony's `'panel/employees/index.html.twig'`.

---

## 10. Configuration — config/*.php

Laravel config files are plain **PHP arrays**, unlike Symfony's YAML.

### Example: `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],
],
```

Access from anywhere via the `config()` helper:

```php
config('app.name');              // "HR Portal"
config('database.default');      // "mysql" (from .env)
```

Compare to Symfony: `$this->container->getParameter('app.name')` or injected via constructor.

---

## 11. The `.env` File

```env
APP_NAME="HR Portal"
APP_ENV=production
APP_KEY=base64:kXxxnS0kq1KtB7Hq...
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=hrportal
DB_USERNAME=hruser
DB_PASSWORD=hrpassword
```

This is like Symfony's `parameters.yaml` (but **never committed to Git**). Access via:

```php
env('DB_HOST')           // In config files
DB::connection()->getDatabaseName()  // Directly in code
```

---

## 12. Dependency Injection — Laravel's Service Container

Laravel has a powerful **Service Container** (like Symfony's DI container). Here's how it works:

### Constructor Injection

```php
class EmployeeController extends Controller
{
    // Laravel automatically injects the dependency
    public function __construct(SomeService $service)
    {
        // ...
    }
}
```

### Container Bindings (in Service Providers)

```php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RepositoryInterface::class, EloquentRepository::class);
    }
}
```

This is like Symfony's `services.yaml` with `autowiring: true`.

---

## 13. Artisan — Laravel's CLI

```bash
php artisan serve              # Start dev server
php artisan migrate            # Run migrations
php artisan db:seed            # Run seeders
php artisan make:controller EmployeeController  # Generate a controller
php artisan make:model Employee -m          # Generate model + migration
php artisan route:list         # Show all routes
php artisan cache:clear        # Clear config cache
php artisan optimize           # Rebuild optimized autoloaders
php artisan key:generate       # Generate APP_KEY
```

This is like Symfony's `bin/console` commands.

---

## 14. Eloquent Query Builder — Common Patterns

From your app, here are the most common patterns:

```php
// Basic query
$employees = Employee::all();           // SELECT * FROM employees
$employee = Employee::find(1);          // SELECT * FROM employees WHERE id = 1

// Where clauses
$active = Employee::where('status', 'active')->get();
$mixed = Employee::where('status', 'active')
                   ->where('department', 'Engineering')
                   ->get();
$or_query = Employee::where(function ($q) {
    $q->where('first_name', 'like', '%john%')
      ->orWhere('last_name', 'like', '%john%');
})->get();

// Ordering
$ordered = Employee::orderBy('last_name')->get();
$orderedDesc = Employee::orderByDesc('created_at')->get();

// Pagination (ready for Blade)
$paginated = Employee::paginate(15);  // ->links() in Blade for pagination HTML
$paginatedWithQuery = Employee::paginate(15)->withQueryString();  // Keep ?search=X in links

// Counting
$role->users()->count();              // SELECT COUNT(*) FROM user_role WHERE role_id = ?

// Existence check
$role->users()->exists();             // SELECT 1 ... LIMIT 1 (optimized)

// Load relationships (Eager Loading — avoids N+1)
$users = User::with('roles')->get();  // 2 queries instead of N+1

// Update
$employee->update(['status' => 'inactive']);  // UPDATE employees SET ... WHERE id = ?
$employee->delete();                        // DELETE FROM employees WHERE id = ?

// Accessors (virtual attributes)
echo $employee->full_name;           // Calls getFullNameAttribute()

// Casts (automatic type conversion)
echo $employee->hire_date->format('Y-m-d');  // Carbon instance
echo number_format($employee->salary, 2);      // Float

// Pivot table operations (many-to-many)
$user->roles()->attach([1, 2, 3]);       // INSERT into user_role
$user->roles()->sync([2, 3]);            // DELETE old + INSERT new (sync = full replacement)
$user->roles()->detach([1]);             // DELETE from user_role
```

---

## 15. Testing

### Your Test Setup

```php
// tests/TestCase.php (base class)
class TestCase extends RefreshDatabase  // Databases are refreshed between tests
{
    // ...
}
```

```php
// Feature test
class ExampleTest extends TestCase
{
    public function testBasic(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
```

### Testing Patterns

```php
// Create test data
$admin = User::factory()->create([
    'email' => 'admin@test.local',
]);

// Assert response
$this->get('/panel/employees')
    ->assertStatus(200)
    ->assertSee('Employees');

// Assert redirected
$this->post('/panel/employees', $data)
    ->assertRedirect(route('employees.index'));

// Assert JSON
$this->getJson('/api/users')
    ->assertJsonStructure(['data' => [[]]]);

// Refresh database between tests (truncates + re-migrates)
class TestCase extends RefreshDatabase { }
```

Run tests:

```bash
php artisan test
```

This is like Symfony's `bin/phpunit`.

---

## 16. Lifecycle Comparison — Request Flow

### Symfony Flow
```
public/index.php
  → Kernel::handle(Request)
    → Router: match URL → route (from attributes or routes.yaml)
    → Firewall: authenticate
    → Controller method called
    → Controller calls services (DI)
    → Controller calls Doctrine (EntityManager)
    → Controller renders Twig template
  → Response sent
```

### Laravel Flow (your app)
```
public/index.php
  → bootstrap/app.php (configure routing, middleware, exceptions)
  → Router: match URL → route (from routes/web.php)
  → Middleware chain (auth, role)
  → Controller method called
  → Controller calls Eloquent (Model::query())
  → Controller returns view() helper
  → Blade template compiled to PHP + rendered
  → Response sent
```

---

## 17. Common Pitfalls for Symfony Developers

| Pitfall | Explanation | Solution |
|---------|-------------|----------|
| **No entity constraints** | Eloquent doesn't validate at the model level — all validation is in controllers (`$request->validate()`) | Don't forget to call `validate()` in every create/update method |
| **Mass assignment** | Forgetting `$fillable` = security vulnerability | Always define `$fillable` on every model |
| **N+1 queries** | `foreach($users as $u) echo $u->role->name;` fires N queries | Use eager loading: `User::with('role')->get()` |
| **Route not found** | Forgetting `->name('route.name')` | Always name your routes — you can't reverse-lookup by controller/method like Symfony |
| **Blade cache** | Views cached in `storage/framework/views/` — old changes not showing | Clear with `php artisan view:clear` or delete the folder |
| **`abort()` exceptions** | `abort(404)` in middleware or controllers still works — Laravel catches it | Just use `abort($statusCode, $message)` |
| **PHP 8.3+ typed properties** | Eloquent uses magic `__get()`/`__set()` — don't declare typed properties on models | Define `$fillable`, `$hidden`, `$casts` instead of typed properties |
| **Relationships inferred** | `belongsToMany(Role::class)` guesses pivot table as `role_user` (alphabetical) | Name it explicitly: `belongsToMany(Role::class, 'user_role')` |
| **`$this->getUser()`** | No `AbstractController` with this method | Use `auth()->user()` helper instead |
| **Transactions** | `DB::transaction(function() { ... })` — Laravel wraps in transaction automatically | Use when doing multiple DB operations that should all succeed or fail |

---

## 18. Quick Reference: Symfony → Laravel

| Symfony Concept | Laravel Equivalent |
|-----------------|-------------------|
| `#[Route('/path', methods: ['GET'])]` | `Route::get('/path', [Ctrl::class, 'method'])` |
| `#[GetMapping]`, `#[PostMapping]` | `Route::get()`, `Route::post()` |
| `config/packages/xxx.yaml` | `config/xxx.php` (PHP arrays) |
| `config/bundles.php` | `bootstrap/providers.php` |
| `src/Kernel::registerBundles()` | `bootstrap/app.php` |
| `#[Entity]`, `#[Column]` annotations | Plain PHP model class with `$fillable` |
| `EntityManager` | `Model::all()`, `Model::find()`, `Model::where()` |
| DQL | Query Builder / Eloquent chains |
| `#[ManyToOne]`, `#[OneToMany]` | `belongsTo()`, `hasMany()`, `belongsToMany()` |
| `#[Id] #[GeneratedValue]` | `$table->id()` in migrations |
| `#[ORM\OnDelete("SET NULL")]` | `->nullOnDelete()` in migrations |
| `#[UniqueEntity]` | `'unique:table'` in `$request->validate()` |
| `AbstractController::render()` | `view('path', compact('vars'))` |
| `AbstractController::redirect()` | `redirect()->route('name')` |
| `$this->getUser()` | `auth()->user()` |
| `$this->isCsrfTokenValid()` | `@csrf` Blade directive |
| `config/routes.yaml` | `routes/web.php` |
| `bin/console` | `php artisan` |
| `doctrine:migrations` | `php artisan migrate` |
| `doctrine:fixtures:load` | `php artisan db:seed` |
| `XStatic/Validator` | `$request->validate(['field' => 'rules'])` |
| `#[FormType]` classes | `$request->validate()` in controllers (no form classes) |
| `#[ParamConverter]` | Route model binding (`Employee $employee`) |
| Custom listener/subscriber | Events/Listeners or middleware |
| `#[Voter]` | Middleware (`role:admin`) |
| `services.yaml` (autowiring) | Constructor injection + Service Providers |
| `CacheInterface` | `Cache::store()->get()` |
| `LoggerInterface` | `Log::info()` facade or `log()` helper |
| `Serializer` | `$model->toArray()` (no serializer needed) |
| Twig extension | Blade directive (`@foreach`, `@if`, `@stack`) |
| `@import "config/routes.yaml"` | Route grouping with `Route::middleware()->group()` |

---

## 19. File Reference Index (in Your App)

| Concept | File |
|---------|------|
| Application bootstrap | `bootstrap/app.php` |
| Service providers | `bootstrap/providers.php` |
| Web routes | `routes/web.php` |
| Console routes | `routes/console.php` |
| Entry point (web) | `public/index.php` |
| Entry point (CLI) | `artisan` |
| Base controller | `app/Http/Controllers/Controller.php` |
| Employee CRUD | `app/Http/Controllers/EmployeeController.php` |
| User CRUD | `app/Http/Controllers/UserController.php` |
| Role CRUD | `app/Http/Controllers/RoleController.php` |
| Login/Auth | `app/Http/Controllers/Auth/LoginController.php` |
| Role middleware | `app/Http/Middleware/RoleMiddleware.php` |
| App service provider | `app/Providers/AppServiceProvider.php` |
| User model | `app/Models/User.php` |
| Employee model | `app/Models/Employee.php` |
| Role model | `app/Models/Role.php` |
| Auth config | `config/auth.php` |
| Database config | `config/database.php` |
| Session config | `config/session.php` |
| Filesystem config | `config/filesystem.php` |
| App config | `config/app.php` |
| Layout template | `resources/views/layouts/app.blade.php` |
| Login page | `resources/views/auth/login.blade.php` |
| Employee list | `resources/views/panel/employees/index.blade.php` |
| User migration | `database/migrations/0001_01_01_000000_create_users_table.php` |
| Role migration | `database/migrations/2025_01_01_000001_create_roles_table.php` |
| Pivot migration | `database/migrations/2025_01_01_000002_create_user_role_table.php` |
| Employee migration | `database/migrations/2025_01_01_000003_create_employees_table.php` |
| Database seeder | `database/seeders/DatabaseSeeder.php` |
| Role seeder | `database/seeders/AdminRoleSeeder.php` |
| User seeder | `database/seeders/AdminUserSeeder.php` |
| User factory | `database/factories/UserFactory.php` |
| Environment config | `.env` |
| Composer config | `composer.json` |

---

*Generated from your Laravel 13 HR Portal project. Laravel documentation: https://laravel.com/docs*
