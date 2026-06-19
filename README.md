<!-- This project was entirely generated using the local model **Qwen3.6-35B-A3B-4bit** and the coding agent **pi**. No external AI APIs were used. -->

# HR Portal — Built entirely with local LLM (Qwen3.6-35B-A3B-4bit + pi)

A Laravel-based web application for managing occupational health referrals, employee records, companies, and exposure factors. Built with **Laravel 13** on **PHP 8.3** with role-based access control (Admin / standard users).

This project was created end-to-end using the local model **Qwen3.6-35B-A3B-4bit** and the coding agent **pi** — no cloud AI services or external paid APIs were involved. It serves as proof that powerful, production-quality Laravel applications can be built entirely with local LLMs.

## Features

- **Authentication** — Login/logout with session-based auth
- **Employee Management** — Full CRUD for employees (name, email, position, department, PESEL, hire date, salary, status, address, notes), linked to companies
- **Company Management** — Full CRUD for companies (name, NIP, address)
- **Exposure Factors & Categories** — Define occupational exposure categories and factors
- **Referrals** — Create occupational health referrals linked to employees, assign exposure factors, and **generate/download PDF** reports (via dompdf)
- **Dashboard** — Overview at `/panel/dashboard`
- **User & Role Management** — Admin-only CRUD for users and roles
- **Role Middleware** — `role:admin` guard restricts sensitive routes

## Database (MySQL)

| Table | Description |
|---|---|
| `users` | Authentication users |
| `roles` | Roles (e.g. admin) |
| `user_role` | Many-to-many user ↔ role |
| `companies` | Company info (name, NIP, address) |
| `employees` | Employee records (linked to company) |
| `exposure_categories` | Exposure categories (code, name, sort order) |
| `exposure_factors` | Specific exposure factors (linked to category) |
| `referrals` | Occupational health referrals (type, job details, linked to employee) |
| `referral_exposure_factors` | Many-to-many referral ↔ exposure factor with extra details |

## API Overview (authenticated routes)

| Prefix | Access | Description |
|---|---|---|
| `GET /panel/dashboard` | Any authenticated | Dashboard |
| `GET /panel/employees` (CRUD) | Any authenticated | Employee management |
| `GET /panel/companies` (CRUD) | Any authenticated | Company management |
| `GET /panel/exposure-factors` (CRUD) | Any authenticated | Exposure factor management |
| `GET /panel/referrals` (CRUD + PDF) | Any authenticated | Referral management with PDF generation |
| `GET /panel/users` (CRUD) | **Admin only** | User management |
| `GET /panel/roles` (CRUD) | **Admin only** | Role management |

## Project Structure

```
LaravelPi/
├── app/
│   ├── Http/Controllers/    # Auth, Company, Dashboard, Employee,
│   │                          ExposureFactor, Referral, Role, User
│   ├── Http/Middleware/     # RoleMiddleware
│   ├── Models/              # User, Role, Company, Employee,
│   │                          ExposureCategory, ExposureFactor,
│   │                          Referral, ReferralExposureFactor
│   └── Services/            # AuthService, CompanyService,
│                              EmployeeService, ExposureFactorService,
│                              ReferralService, RoleService, UserService
├── database/
│   ├── migrations/          # Schema for all tables
│   ├── seeders/             # Admin user, roles, sample data
│   └── database.sqlite      # SQLite (local development)
├── resources/views/panel/   # Blade templates for all panels
├── routes/web.php           # All routes
├── docker/
│   ├── docker-compose.yml   # 3 services: app, nginx, mysql
│   └── nginx/default.conf   # Nginx config
└── storage/app/             # Uploaded files & generated PDFs
```

## Prerequisites

- **Docker** and **Docker Compose**
- **PHP 8.3+** (for local development outside Docker)
- **Node.js / npm** (for Vite asset compilation)

## Running the Application

### Using Docker (recommended)

```bash
# 1. Start containers (builds + runs)
docker compose up -d --build

# 2. Install PHP dependencies (one-time)
docker compose exec laravel_app composer install --no-interaction

# 3. Generate application key (one-time)
docker compose exec laravel_app php artisan key:generate --no-interaction

# 4. Run migrations and seed admin data (one-time)
docker compose exec laravel_app php artisan migrate:fresh --seed

# 5. Open in browser
#    http://localhost:8080/login
#    Login: admin@hrportal.local  |  Password: password
```

### Docker-Compose services

| Service   | Container     | Purpose                       | Port |
|-----------|---------------|-------------------------------|------|
| `laravel_app` | `hrportal_app` | Laravel PHP-FPM             | 9000 (internal) |
| `nginx`   | `hrportal_nginx` | Web server (reverse proxy)  | 8080 → 80 |
| `mysql`   | `hrportal_mysql` | MySQL 8.0 database           | 3306 → 3306 |

## Key Endpoints

| URL | Description |
|---|---|
| `http://localhost:8080/login` | Login page |
| `http://localhost:8080/panel/dashboard` | Dashboard |
| `http://localhost:8080/panel/employees` | Employee management |
| `http://localhost:8080/panel/companies` | Company management |
| `http://localhost:8080/panel/referrals` | Referral management |
| `http://localhost:8080/panel/users` | User management (**admin**) |
| `http://localhost:8080/panel/roles` | Role management (**admin**) |

## Default Credentials

```
Email:    admin@hrportal.local
Password: password
```

## Useful Commands

```bash
# Docker
docker compose logs -f          # View logs
docker compose down -v          # Stop + remove volumes (fresh start)
docker compose restart          # Restart containers
docker compose exec laravel_app php artisan migrate:fresh --seed  # Reset data

# PHP / Laravel
php artisan serve               # Local dev server
php artisan tinker              # Interactive shell
php artisan migrate:fresh --seed  # Fresh DB + seeders

# Tests
php artisan test                # Run PHPUnit tests

# Asset building
npm run build                   # Production build
npm run dev                     # Vite HMR
```

## Tech Stack

- **PHP 8.3** — Language runtime
- **Laravel 13** — PHP framework
- **MySQL 8.0** — Database (production & local via Docker)
- **Nginx** — Reverse proxy / web server
- **Vite** — Front-end asset bundler
- **dompdf** — PDF report generation
- **Blade** — Server-side templating
- **MySQL** (via `doctrine/dbal`) — Schema introspection
