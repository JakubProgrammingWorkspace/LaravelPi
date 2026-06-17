<?php

namespace App\Http\Controllers;

use App\Models\ExposureCategory;
use Illuminate\View\View;

class ExposureCategoryController extends Controller
{
    /**
     * Display all exposure categories with their factors.
     */
    public function index(): View
    {
        $categories = ExposureCategory::ordered()
            ->with(['factors' => fn($q) => $q->orderBy('name')])
            ->get();

        return view('panel.exposure-factors.index', compact('categories'));
    }
}
