<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Contribution extends Model
{
    public $timestamps = false;

    /**
     * Get the business trips associated with the contribution type
     *
     * @return BelongsToMany
     */
    public function businessTrips(): BelongsToMany
    {
        return $this->belongsToMany(BusinessTrip::class, TripContribution::class)
            ->withPivot(['detail', 'created_at', 'updated_at']);
    }
}
