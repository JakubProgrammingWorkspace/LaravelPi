<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    /**
     * Search and paginate employees with optional filters.
     */
    public function search(
        string $search = '',
        string $status = '',
        string $department = '',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Employee::orderBy('last_name');

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status !== '') {
            $query->where('status', $status);
        }

        // Department filter
        if ($department !== '') {
            $query->where('department', $department);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get a list of all departments for filter dropdowns.
     */
    public function getDepartments(): array
    {
        return Employee::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->toArray();
    }

    /**
     * Create a new employee, recording the creator.
     */
    public function create(array $data): Employee
    {
        return Employee::create(array_merge($data, [
            'created_by' => auth()->id(),
        ]));
    }

    /**
     * Update an existing employee, recording the updater.
     */
    public function update(Employee $employee, array $data): Employee
    {
        $employee->update(array_merge($data, [
            'updated_by' => auth()->id(),
        ]));

        return $employee;
    }

    /**
     * Delete an employee.
     */
    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }
}
