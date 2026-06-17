<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ExposureCategory;
use App\Models\ExposureFactor;
use App\Models\Referral;
use App\Models\ReferralExposureFactor;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralController extends Controller
{
    public function __construct(private readonly ReferralService $referralService)
    {
    }

    /**
     * Display a listing of referrals.
     */
    public function index(): View
    {
        $referrals = $this->referralService->list();

        return view('panel.referrals.index', compact('referrals'));
    }

    /**
     * Show the form for creating a new referral.
     */
    public function create(): View
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get();
        $categories = ExposureCategory::ordered()
            ->with(['factors' => fn($q) => $q->orderBy('name')])
            ->get();

        return view('panel.referrals.create', compact('employees', 'categories'));
    }

    /**
     * Store a newly created referral.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'examination_type' => 'required|in:wstępne,okresowe,kontrolne',
            'job_position'     => 'nullable|string|max:255',
            'job_description'  => 'nullable|string',
            'issue_place'      => 'nullable|string|max:255',
            'issue_date'       => 'required|date',
            'exposure_factor_ids' => 'nullable|array',
            'exposure_factor_ids.*' => 'exists:exposure_factors,id',
            'exposure_details.*'  => 'nullable|string',
        ], [
            'examination_type.in' => 'Typ badania musi być: wstępne, okresowe lub kontrolne.',
        ]);

        $referral = $this->referralService->create($validated);

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Skierowanie utworzone pomyślnie.');
    }

    /**
     * Display the specified referral.
     */
    public function show(Referral $referral): View
    {
        $referral->load(['employee', 'exposureFactors.exposureFactor', 'creator']);

        return view('panel.referrals.show', compact('referral'));
    }

    /**
     * Generate PDF for a referral.
     */
    public function generatePdf(Referral $referral): RedirectResponse
    {
        $pdfPath = $this->referralService->generatePdf($referral);

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'PDF został wygenerowany pomyślnie.');
    }

    /**
     * Download / stream the PDF.
     */
    public function downloadPdf(Referral $referral): \Illuminate\Http\BinaryFileResponse|\Illuminate\Http\Response
    {
        $pdfPath = $this->referralService->downloadPdf($referral);

        // If no PDF exists, return 404
        return $pdfPath;
    }

    /**
     * Remove the specified referral.
     */
    public function destroy(Referral $referral): RedirectResponse
    {
        $this->referralService->delete($referral);

        return redirect()->route('referrals.index')
            ->with('success', 'Skierowanie usunięte pomyślnie.');
    }
}
