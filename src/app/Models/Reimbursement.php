<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reimbursement extends Model
{
    use HasFactory;

    protected $casts = [
        'reimbursement_date' => 'date'
    ];

    /**
     * Get the business trip associated with the reimbursement
     *
     * @return HasOne
     */
    public function businessTrip(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'reimbursement_id');
    }

    /**
     * Get the SPP symbol associated with the reimbursement
     *
     * @return BelongsTo
     */
    public function sppSymbol(): BelongsTo
    {
        return $this->belongsTo(SppSymbol::class);
    }
}
