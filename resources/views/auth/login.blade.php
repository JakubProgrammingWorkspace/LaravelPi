<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'HR Portal') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            min-height: 100vh;
        }
        .login-card {
            max-width: 420px;
            margin: 5rem auto;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h1 class="text-primary mb-1">
                        <i class="bi bi-building"></i>
                    </h1>
                    <h4 class="text-muted">{{ config('app.name', 'HR Portal') }}</h4>
                    <p class="text-muted small">Sign in to your account</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   placeholder="you@company.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   required
                                   placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>

                <hr>
                <p class="text-center text-muted small mb-0">
                    <a href="/" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back to Home
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
