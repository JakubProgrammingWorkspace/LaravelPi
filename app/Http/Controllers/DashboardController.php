<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Referral;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with summary counts.
     */
    public function index(): View
    {
        $data = [
            'companies'          => Company::count(),
            'employees'          => Employee::count(),
            'referrals'          => Referral::count(),
            'referralsWithPdf'   => Referral::whereNotNull('pdf_path')->whereNotNull('pdf_generated_at')->count(),
        ];

        return view('panel.dashboard', $data);
    }
}
