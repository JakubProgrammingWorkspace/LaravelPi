<?php

namespace App\Http\Controllers;

use App\Models\ExposureCategory;
use App\Models\ExposureFactor;
use App\Services\ExposureFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExposureFactorController extends Controller
{
    public function __construct(private readonly ExposureFactorService $exposureFactorService)
    {
    }

    /**
     * Display a listing of exposure factors filtered by category.
     */
    public function index(ExposureCategory $exposureCategory = null): View
    {
        $categories = ExposureCategory::ordered()->get();

        if ($exposureCategory) {
            $factors = $exposureCategory->factors()->orderBy('name')->get();
            $title = $exposureCategory->name;
        } else {
            $factors = ExposureFactor::orderBy('name')->get();
            $title = 'Wszystkie czynniki';
        }

        return view('panel.exposure-factors.index', compact('factors', 'categories', 'title'));
    }

    /**
     * Show the form for creating a new factor.
     */
    public function create(): View
    {
        $categories = ExposureCategory::ordered()->get();
        return view('panel.exposure-factors.create', compact('categories'));
    }

    /**
     * Store a newly created exposure factor.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exposure_category_id' => 'required|exists:exposure_categories,id',
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
        ]);

        $this->exposureFactorService->create($validated);

        return redirect()->route('exposure-factors.index')
            ->with('success', 'Czynnik narażenia dodany pomyślnie.');
    }

    /**
     * Remove the specified factor (check referrals first).
     */
    public function destroy(ExposureFactor $factor): RedirectResponse
    {
        $this->exposureFactorService->delete($factor);

        return redirect()->route('exposure-factors.index')
            ->with('success', 'Czynnik narażenia usunięty pomyślnie.');
    }
}
