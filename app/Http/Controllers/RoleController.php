<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of all roles.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->paginate(15);
        return view('panel.roles.index', compact('roles'));
    }

    /**
     * Display the specified role with its users.
     */
    public function show(Role $role): View
    {
        $role->load('users');
        return view('panel.roles.show', compact('role'));
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Check if role is assigned to any user
        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete role: it is assigned to ' . $role->users()->count() . ' user(s).');
        }

        $role->delete();
        return back()->with('success', 'Role deleted successfully.');
    }
}
