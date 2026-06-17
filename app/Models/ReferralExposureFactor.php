<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralExposureFactor extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'referral_id',
        'exposure_factor_id',
        'exposure_details',
    ];

    /**
     * BelongsTo: The referral.
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * BelongsTo: The exposure factor.
     */
    public function exposureFactor(): BelongsTo
    {
        return $this->belongsTo(ExposureFactor::class);
    }
}
