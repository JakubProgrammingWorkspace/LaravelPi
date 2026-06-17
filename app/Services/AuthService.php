<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Attempt to authenticate a user with email/password.
     *
     * @return array{success: bool, user?: \App\Models\User}
     */
    public function attempt(array $credentials, bool $remember = false): array
    {
        if (Auth::attempt($credentials, $remember)) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    /**
     * Log out the current user and invalidate the session.
     */
    public function logout(\Illuminate\Http\Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
