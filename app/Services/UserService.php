<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Search and paginate users with optional filtering.
     */
    public function search(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new user with optional roles.
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->roles()->attach($data['roles']);
        }

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user;
    }

    /**
     * Delete a user (with self-deletion guard).
     *
     * @return bool|null True on success, or null if the user tried to delete themselves.
     */
    public function delete(User $user): ?bool
    {
        if ($user->id === auth()->id()) {
            return null;
        }

        return $user->delete();
    }
}
