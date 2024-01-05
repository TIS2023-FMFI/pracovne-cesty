<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use HasFactory;

    protected $casts = [
        'reimburse' => 'boolean'
    ];

    /**
     * Get the business trips where the expense acts as an accommodation expense
     *
     * @return HasOne
     */
    public function accommodation(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'accommodation_expense_id');
    }

    /**
     * Get the business trips where the expense acts as a travelling expense
     *
     * @return HasOne
     */
    public function travelling(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'travelling_expense_id');
    }


    /**
     * Get the business trips where the expense acts as other expense
     *
     * @return HasOne
     */
    public function other(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'other_expense_id');
    }

    /**
     * Get the business trips where the expense acts as advance payment expense
     *
     * @return HasOne
     */
    public function advance(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'advance_expense_id');
    }

    /**
     * Get the business trips where the expense acts as an allowance
     *
     * @return HasOne
     */
    public function allowance(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'allowance_expense_id');
    }

    /**
     * Get the business trip associated with the expense
     *
     * @return Collection
     */
    public function businessTrip(): Collection
    {
        $trips = new Collection([]);

        foreach ([$this->accommodation(), $this->travelling(), $this->other(), $this->allowance()] as $trip) {
            $trips = $trips->merge($trip->get());
        }

        return $trips;
    }
}
