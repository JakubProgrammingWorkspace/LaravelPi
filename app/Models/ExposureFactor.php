<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExposureFactor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'exposure_category_id',
        'name',
        'description',
    ];

    /**
     * BelongsTo: The exposure category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExposureCategory::class, 'exposure_category_id');
    }

    /**
     * BelongsToMany: Exposure factors can be linked to many referrals.
     */
    public function referrals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Referral::class, 'referral_exposure_factors')
            ->withPivot('exposure_details')
            ->withTimestamps();
    }
}
