<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    protected $casts = [
        'reimburse' => 'boolean'
    ];

    /**
     * Get the business trips where the expense acts as an accommodation expense
     *
     * @return HasMany
     */
    public function accommodation(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'accommodation_expense_id');
    }

    /**
     * Get the business trips where the expense acts as a travelling expense
     *
     * @return HasMany
     */
    public function travelling(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'travelling_expense_id');
    }


    /**
     * Get the business trips where the expense acts as other expense
     *
     * @return HasMany
     */
    public function other(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'other_expense_id');
    }

    /**
     * Get the business trips where the expense acts as an allowance
     *
     * @return HasMany
     */
    public function allowance(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'allowance_id');
    }

    /**
     * Get the business trips associated with the expense
     *
     * @return Collection
     */
    public function businessTrips(): Collection
    {
        $trips = new Collection([]);

        foreach ([$this->accommodation(), $this->travelling(), $this->other(), $this->allowance()] as $trip) {
            $trips = $trips->merge($trip->get());
        }

        return $trips;
    }
}
