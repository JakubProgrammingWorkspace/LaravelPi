<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
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

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        // Get departments for filter dropdown
        $departments = Employee::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->toArray();

        $employees = $query->paginate(15)->withQueryString();
        return view('panel.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        return view('panel.employees.create');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:employees,email',
            'position'   => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'hire_date'  => 'nullable|date',
            'salary'     => 'nullable|numeric|min:0',
            'status'     => 'required|in:active,inactive,terminated',
            'notes'      => 'nullable|string',
        ]);

        $employee = Employee::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee added successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): View
    {
        return view('panel.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the employee.
     */
    public function edit(Employee $employee): View
    {
        return view('panel.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => "nullable|email|max:255|unique:employees,email,{$employee->id}",
            'position'   => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'hire_date'  => 'nullable|date',
            'salary'     => 'nullable|numeric|min:0',
            'status'     => 'required|in:active,inactive,terminated',
            'notes'      => 'nullable|string',
        ]);

        $employee->update(array_merge($validated, [
            'updated_by' => auth()->id(),
        ]));

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employee removed successfully.');
    }
}
