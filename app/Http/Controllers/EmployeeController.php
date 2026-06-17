<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
            perPage:    15,
            companyId:  $request->input('company_id') ? (int) $request->input('company_id') : null,
        );

        $departments = $this->employeeService->getDepartments();
        $companies = Company::orderBy('name')->get();

        return view('panel.employees.index', compact('employees', 'departments', 'companies'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $companies = Company::orderBy('name')->get();
        return view('panel.employees.create', compact('companies'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'pesel'         => 'nullable|regex:/^[0-9]{11}$/|unique:employees,pesel',
            'email'         => 'nullable|email|max:255|unique:employees,email',
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'company_id'    => 'nullable|exists:companies,id',
            'position'      => 'nullable|string|max:255',
            'department'    => 'nullable|string|max:255',
            'hire_date'     => 'nullable|date',
            'salary'        => 'nullable|numeric|min:0',
            'status'        => 'required|in:active,inactive,terminated',
            'notes'         => 'nullable|string',
        ], [
            'pesel.regex'  => 'PESEL musi składać się z dokładnie 11 cyfr.',
            'pesel.unique' => 'Ten PESEL jest już przypisany innemu pracownikowi.',
        ]);

        $employee = $this->employeeService->create($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Pracownik dodany pomyślnie.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): View
    {
        $employee->load('company');
        return view('panel.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the employee.
     */
    public function edit(Employee $employee): View
    {
        $companies = Company::orderBy('name')->get();
        return view('panel.employees.edit', compact('employee', 'companies'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'pesel'         => "nullable|regex:/^[0-9]{11}$/|unique:employees,pesel,{$employee->id}",
            'email'         => "nullable|email|max:255|unique:employees,email,{$employee->id}",
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'company_id'    => 'nullable|exists:companies,id',
            'position'      => 'nullable|string|max:255',
            'department'    => 'nullable|string|max:255',
            'hire_date'     => 'nullable|date',
            'salary'        => 'nullable|numeric|min:0',
            'status'        => 'required|in:active,inactive,terminated',
            'notes'         => 'nullable|string',
        ], [
            'pesel.regex'  => 'PESEL musi składać się z dokładnie 11 cyfr.',
            'pesel.unique' => 'Ten PESEL jest już przypisany innemu pracownikowi.',
        ]);

        $this->employeeService->update($employee, $validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Pracownik zaktualizowany pomyślnie.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $this->employeeService->delete($employee);

        return redirect()->route('employees.index')
            ->with('success', 'Pracownik usunięty pomyślnie.');
    }
}
