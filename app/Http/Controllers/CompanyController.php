<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(private readonly CompanyService $companyService)
    {
    }

    /**
     * Display a listing of companies.
     */
    public function index(Request $request): View
    {
        $companies = $this->companyService->search(
            search:  $request->input('search', ''),
            perPage: 15
        );

        return view('panel.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View
    {
        return view('panel.companies.create');
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'nip'         => 'nullable|numeric|min:1000000000|max:9999999999|size:10|unique:companies,nip',
            'street'      => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ], [
            'nip.size'   => 'NIP musi mieć dokładnie 10 cyfr.',
            'nip.min'    => 'Nieprawidłowy format NIP.',
            'nip.unique' => 'Ten NIP jest już przypisany do innej firmy.',
        ]);

        $company = $this->companyService->create($validated);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Firma dodana pomyślnie.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): View
    {
        return view('panel.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the company.
     */
    public function edit(Company $company): View
    {
        return view('panel.companies.edit', compact('company'));
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'nip'         => "nullable|numeric|min:1000000000|max:9999999999|size:10|unique:companies,nip,{$company->id}",
            'street'      => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ], [
            'nip.size'   => 'NIP musi mieć dokładnie 10 cyfr.',
            'nip.min'    => 'Nieprawidłowy format NIP.',
            'nip.unique' => 'Ten NIP jest już przypisany do innej firmy.',
        ]);

        $this->companyService->update($company, $validated);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Firma zaktualizowana pomyślnie.');
    }

    /**
     * Remove the specified company.
     */
    public function destroy(Company $company): RedirectResponse
    {
        $this->companyService->delete($company);

        return redirect()->route('companies.index')
            ->with('success', 'Firma usunięta pomyślnie.');
    }
}
