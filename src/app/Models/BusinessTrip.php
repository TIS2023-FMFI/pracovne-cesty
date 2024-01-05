<?php

namespace App\Models;

use App\Enums\TripState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BusinessTrip extends Model
{
    use HasFactory;

    protected $casts = [
        'state' => TripState::class,

        'datetime_start' => 'datetime',
        'datetime_end' => 'datetime',

        'datetime_border_crossing_start' => 'datetime',
        'datetime_border_crossing_end' => 'datetime',

        'meals_reimbursement' => 'boolean'
    ];

    // Foreign relationships

    /**
     * Get the user associated with the business trip
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination country of the business trip
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the means of transport used for the business trip
     *
     * @return BelongsTo
     */
    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class);
    }

    /**
     * Get the purpose of the business trip
     *
     * @return BelongsTo
     */
    public function tripPurpose(): BelongsTo
    {
        return $this->belongsTo(TripPurpose::class);
    }

    /**
     * Get the conference fee associated with the business trip
     *
     * @return BelongsTo
     */
    public function conferenceFee(): BelongsTo
    {
        return $this->belongsTo(ConferenceFee::class);
    }

    /**
     * Get the reimbursement record associated with the business trip
     *
     * @return BelongsTo
     */
    public function reimbursement(): BelongsTo
    {
        return $this->belongsTo(Reimbursement::class);
    }

    /**
     * Get the SPP symbol associated with the business trip
     *
     * @return BelongsTo
     */
    public function sppSymbol(): BelongsTo
    {
        return $this->belongsTo(SppSymbol::class);
    }

    /**
     * Get the accommodation expense of the business trip
     *
     * @return BelongsTo
     */
    public function accommodationExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'accommodation_expense_id');
    }

    /**
     * Get the travelling expense of the business trip
     *
     * @return BelongsTo
     */
    public function travellingExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'travelling_expense_id');
    }

    /**
     * Get the other expense of the business trip
     *
     * @return BelongsTo
     */
    public function otherExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'other_expense_id');
    }

    /**
     * Get the advance payment expense of the business trip
     *
     * @return BelongsTo
     */
    public function advanceExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'advance_expense_id');
    }

    /**
     * Get the allowance expense of the business trip
     *
     * @return BelongsTo
     */
    public function allowanceExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'allowance_expense_id');
    }

    /**
     * Get the contributions associated with the business trip
     *
     * @return BelongsToMany
     */
    public function contributions(): BelongsToMany
    {
        return $this->belongsToMany(Contribution::class, TripContribution::class)
            ->withPivot(['detail', 'created_at', 'updated_at']);
    }


    // Accessors

    /**
     * Get all business trip records from the database
     * sorted by the specified key
     *
     * @param array|string $columns
     * @param string $sortBy
     * @return Collection<int, static>
     */
    public static function sortedAll(array|string $columns = ['*'], string $sortBy = 'created_at'): Collection
    {
        return self::all($columns)->sortByDesc($sortBy);
    }

    /**
     * Get all business trip records sorted
     * from the newest to the oldest
     *
     * @param array|string $columns
     * @return Collection
     */
    public static function newest(array|string $columns = ['*']): Collection
    {
        return self::sortedAll($columns, 'created_at');
    }

    /**
     * Get all the unconfirmed business trips from the database
     *
     * @param array|string $columns
     * @return Collection
     */
    public static function unconfirmed(array|string $columns = ['*']): Collection
    {
        return self::select($columns)
            ->where('state', TripState::NEW)
            ->get();
    }

    /**
     * Get all the unaccounted (i.e. completed but not closed) business trips
     * from the database
     *
     * @param array|string $columns
     * @return Collection
     */
    public static function unaccounted(array|string $columns = ['*']): Collection
    {
        return self::select($columns)
            ->where('state', TripState::COMPLETED)
            ->get();
    }
}
