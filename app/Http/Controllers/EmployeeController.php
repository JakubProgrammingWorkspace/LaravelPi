<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService)
    {
    }

    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $employees = $this->employeeService->search(
            search:     $request->input('search', ''),
            status:     $request->input('status', ''),
            department: $request->input('department', ''),
            perPage:    15
        );

        $departments = $this->employeeService->getDepartments();

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

        $employee = $this->employeeService->create($validated);

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

        $this->employeeService->update($employee, $validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $this->employeeService->delete($employee);

        return redirect()->route('employees.index')
            ->with('success', 'Employee removed successfully.');
    }
}
