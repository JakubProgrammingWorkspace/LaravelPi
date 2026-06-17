<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'examination_type',
        'job_position',
        'job_description',
        'issue_place',
        'issue_date',
        'created_by',
        'pdf_path',
        'pdf_generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date'      => 'date',
            'pdf_generated_at' => 'datetime',
        ];
    }

    /**
     * BelongsTo: The employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * BelongsTo: The user who created the referral.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * HasMany: Exposure factors linked to this referral.
     */
    public function exposureFactors(): HasMany
    {
        return $this->hasMany(ReferralExposureFactor::class);
    }

    /**
     * Check if a PDF has been generated for this referral.
     */
    public function hasPdf(): bool
    {
        return !is_null($this->pdf_path)
            && !empty($this->pdf_path)
            && file_exists(storage_path('app/' . $this->pdf_path));
    }

    /**
     * Get the examination type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->examination_type) {
            'wstępne'   => 'Wstępne',
            'okresowe'   => 'Okresowe',
            'kontrolne'  => 'Kontrolne',
            default      => $this->examination_type,
        };
    }
}
