<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'HR Portal'))</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #2c3e50;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.6rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 2px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
        }
        .sidebar .nav-link i {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.5rem;
        }
        .main-content {
            padding: 1.5rem;
        }
        .flash-message {
            transition: opacity 0.5s;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-building me-2"></i>{{ config('app.name', 'HR Portal') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar py-3">
                    <div class="position-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                                    <i class="bi bi-people"></i> Employees
                                </a>
                            </li>
                            @if (auth()->check() && auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                        <i class="bi bi-person-gear"></i> Users
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                        <i class="bi bi-shield-check"></i> Roles
                                    </a>
                                </li>
                            @endif
                        </ul>
                        @if (auth()->check() && auth()->user()->isAdmin())
                            <hr class="text-secondary">
                            <div class="px-3">
                                <small class="text-secondary">
                                    <i class="bi bi-shield-lock me-1"></i>Your roles:
                                    @foreach(auth()->user()->roles as $role)
                                        <span class="badge bg-info text-dark me-1">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                </small>
                            </div>
                        @endif
                    </div>
                </nav>
            @endauth

            <!-- Main Content -->
            <main @auth class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" @else class="w-100" @endauth>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
