<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
    /**
     * Search and paginate companies.
     */
    public function search(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = Company::orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new company.
     */
    public function create(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * Update an existing company.
     */
    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        return $company;
    }

    /**
     * Delete a company (caller should guard with hasEmployees first).
     */
    public function delete(Company $company): bool
    {
        return $company->delete();
    }

    /**
     * Check whether a company has employees (for deletion guard).
     */
    public function hasEmployees(Company $company): bool
    {
        return $company->employees()->exists();
    }
}
