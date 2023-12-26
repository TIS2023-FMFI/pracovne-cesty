<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripPurpose extends Model
{
    public $timestamps = false;


    /**
     * Get the business trips associated with the trip purpose
     *
     * @return HasMany
     */
    public function businessTrips(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'trip_purpose_id');
    }
}
