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

    protected $fillable = ['amount_eur', 'amount_foreign', 'reimburse'];

    /**
     * Get the business trips where the expense acts as an accommodation expense (ubytovanie)
     *
     * @return HasOne
     */
    public function accommodation(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'accommodation_expense_id');
    }

    /**
     * Get the business trips where the expense acts as a travelling expense (cestovne)
     *
     * @return HasOne
     */
    public function travelling(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'travelling_expense_id');
    }


    /**
     * Get the business trips where the expense acts as other expense (ine vydavky)
     *
     * @return HasOne
     */
    public function other(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'other_expense_id');
    }

    /**
     * Get the business trips where the expense acts as advance payment expense (zaloha)
     *
     * @return HasOne
     */
    public function advance(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'advance_expense_id');
    }

    /**
     * Get the business trips where the expense acts as an allowance (vreckove)
     *
     * @return HasOne
     */
    public function allowance(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'allowance_expense_id');
    }

    /**
     * Get the business trips where the expense acts as a participation fee (vlozne)
     *
     * @return HasOne
     */
    public function participation(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'participation_expense_id');
    }

    /**
     * Get the business trips where the expense acts as an insurance (poistenie)
     *
     * @return HasOne
     */
    public function insurance(): HasOne
    {
        return $this->hasOne(BusinessTrip::class, 'insurance_expense_id');
    }

    /**
     * Get the business trip associated with the expense
     *
     * @return Collection
     */
    public function businessTrip(): Collection
    {
        $trips = new Collection([]);

        foreach (
            [
                $this->accommodation(), $this->travelling(), $this->other(), $this->advance(),
                $this->allowance(), $this->participation(), $this->insurance()
            ] as $trip
        ) {
            $trips = $trips->merge($trip->get());
        }

        return $trips;
    }
}
