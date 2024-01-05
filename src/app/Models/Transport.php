<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transport extends Model
{
    public $timestamps = false;

    protected $casts = [
        'user_visible' => 'boolean'
    ];

    /**
     * Get the business trips associated with the means of transport
     *
     * @return HasMany
     */
    public function businessTrips(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'transport_id');
    }
}
