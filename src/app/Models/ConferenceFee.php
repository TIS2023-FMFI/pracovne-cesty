<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceFee extends Model
{
    use HasFactory;

    /**
     * Get the business trips associated with the conference fee
     *
     * @return HasMany
     */
    public function businessTrips(): HasMany
    {
        // TODO: Do we accept one conference fee entity for multiple business trips?
        return $this->hasMany(BusinessTrip::class, 'conference_fee_id');
    }
}
