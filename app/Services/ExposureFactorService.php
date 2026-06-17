<?php

namespace App\Services;

use App\Models\ExposureCategory;
use App\Models\ExposureFactor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExposureFactorService
{
    /**
     * List all exposure factors with pagination.
     */
    public function list(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = ExposureFactor::with('category')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new exposure factor.
     */
    public function create(array $data): ExposureFactor
    {
        return ExposureFactor::create($data);
    }

    /**
     * Delete an exposure factor (caller should guard with hasReferrals first).
     */
    public function delete(ExposureFactor $factor): bool
    {
        return $factor->delete();
    }

    /**
     * Check whether an exposure factor is referenced by any referral.
     */
    public function hasReferrals(ExposureFactor $factor): bool
    {
        return ReferralExposureFactor::where('exposure_factor_id', $factor->id)->exists();
    }
}
