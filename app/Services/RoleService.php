<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleService
{
    /**
     * List all roles with user count.
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Role::withCount('users')->orderBy('name')->paginate($perPage);
    }

    /**
     * Load the users for a specific role.
     */
    public function findWithUsers(Role $role): Role
    {
        return $role->load('users');
    }

    /**
     * Check whether a role has assigned users (for deletion guard).
     */
    public function hasAssignedUsers(Role $role): bool
    {
        return $role->users()->exists();
    }

    /**
     * Count assigned users.
     */
    public function countAssignedUsers(Role $role): int
    {
        return $role->users()->count();
    }

    /**
     * Delete a role (caller should guard with hasAssignedUsers first).
     */
    public function delete(Role $role): bool
    {
        return $role->delete();
    }
}
