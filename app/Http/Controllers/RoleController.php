<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService)
    {
    }

    /**
     * Display a listing of all roles.
     */
    public function index(): View
    {
        $roles = $this->roleService->list();
        return view('panel.roles.index', compact('roles'));
    }

    /**
     * Display the specified role with its users.
     */
    public function show(Role $role): View
    {
        $role = $this->roleService->findWithUsers($role);
        return view('panel.roles.show', compact('role'));
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($this->roleService->hasAssignedUsers($role)) {
            return back()->with('error', 'Cannot delete role: it is assigned to ' . $this->roleService->countAssignedUsers($role) . ' user(s).');
        }

        $this->roleService->delete($role);
        return back()->with('success', 'Role deleted successfully.');
    }
}
