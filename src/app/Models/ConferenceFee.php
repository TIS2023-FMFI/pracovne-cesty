<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ConferenceFee extends Model
{
    use HasFactory;

    /**
     * Get the business trip associated with the conference fee
     *
     * @return HasOne
     */
    public function businessTrip(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'conference_fee_id');
    }
}
