<?php

namespace App\Models;

use App\Enums\TripState;
use App\Enums\TripType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class BusinessTrip extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => TripType::class,
        'state' => TripState::class,

        'datetime_start' => 'datetime',
        'datetime_end' => 'datetime',

        'datetime_border_crossing_start' => 'datetime',
        'datetime_border_crossing_end' => 'datetime',

        'meals_reimbursement' => 'boolean',
        'is_template' => 'boolean'
    ];

    protected $fillable = [
        'user_id', 'type', 'country_id', 'transport_id', 'place', 'event_url', 'upload_name', 'sofia_id',
        'state', 'datetime_start', 'datetime_end', 'place_start', 'place_end', 'datetime_border_crossing_start',
        'datetime_border_crossing_end', 'trip_purpose_id', 'purpose_details', 'iban', 'conference_fee_id',
        'reimbursement_id', 'spp_symbol_id', 'spp_symbol_id_2', 'spp_symbol_id_3', 'amount_eur', 'amount_eur_2',
        'amount_eur_3', 'travelling_expense_id', 'accommodation_expense_id',
        'participation_expense_id', 'insurance_expense_id', 'other_expense_id', 'allowance_expense_id',
        'advance_expense_id', 'not_reimbursed_meals', 'meals_reimbursement', 'expense_estimation',
        'cancellation_reason', 'note', 'conclusion', 'is_template'
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
     * Get the travelling expense of the business trip
     *
     * @return BelongsTo
     */
    public function travellingExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'travelling_expense_id');
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
     * Get the participation fee expense of the business trip
     *
     * @return BelongsTo
     */
    public function participationExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'participation_expense_id');
    }

    /**
     * Get the insurance expense of the business trip
     *
     * @return BelongsTo
     */
    public function insuranceExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'insurance_expense_id');
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
     * Get the allowance expense of the business trip
     *
     * @return BelongsTo
     */
    public function allowanceExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'allowance_expense_id');
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
     * Get all business trip records
     *
     * @param array|string $columns
     * @return Builder
     */
    public static function getAll(array|string $columns = ['*']): Builder
    {
        return self::select($columns);
    }

    /**
     * Get all business trips in the specified state
     *
     * @param TripState $state
     * @param array|string $columns
     * @return Builder
     */
    protected static function getByState(TripState $state, array|string $columns = ['*']): Builder
    {
        return self::select($columns)
            ->where('state', $state);
    }

    /**
     * Get all the unconfirmed business trips from the database
     *
     * @param array|string $columns
     * @return Builder
     */
    public static function unconfirmed(array|string $columns = ['*']): Builder
    {
        return self::getByState(TripState::NEW);
    }

    /**
     * Get all the unaccounted (i.e. completed but not closed) business trips
     * from the database
     *
     * @param array|string $columns
     * @return Builder
     */
    public static function unaccounted(array|string $columns = ['*']): Builder
    {
        return self::getByState(TripState::COMPLETED);
    }

    /**
     * Get all business trips of the specified type
     *
     * @param TripType $type
     * @param array|string $columns
     * @return Builder
     */
    protected static function getByType(TripType $type, array|string $columns = ['*']): Builder
    {
        return self::select($columns)
            ->where('type', $type);
    }

    /**
     * Get domestic business trips
     *
     * @param array|string $columns
     * @return Builder
     */
    public static function domestic(array|string $columns = ['*']): Builder
    {
        return self::getByType(TripType::DOMESTIC, $columns);
    }

    /**
     * Get foreign business trips
     *
     * @param array|string $columns
     * @return Builder
     */
    public static function foreign(array|string $columns = ['*']): Builder
    {
        return self::getByType(TripType::FOREIGN, $columns);
    }

    /**
     * Check if a given sofia_id already exists in the database.
     * Allow for duplicates of the value '0000'.
     *
     * @param $sofiaId
     * @param $tripId
     * @return boolean
     */
    public static function isDuplicateSofiaId($sofiaId, $tripId = null)
    {
        // Allow multiple '0000' values
        if ($sofiaId === '0000') {
            return false;
        }

        // Check for duplicates, excluding the current trip ID (for updates)
        return self::where('sofia_id', $sofiaId)
            ->when($tripId, function ($query, $tripId) {
                $query->where('id', '!=', $tripId);
            })
            ->exists();
    }

     /**
     * Checks if there is another trip in database with same user id, place, start date and end date
     * that is not cancelled
     *
     * @param int $user_id
     * @param string $place
     * @param string $datetime_start
     * @param string $datetime_end
     * @return boolean
     */
     public static function isDuplicate(int $user_id, string $place, string $datetime_start, string $datetime_end)
     {
         $duplicates = self::select()
         ->where('user_id', $user_id)
         ->where('place', $place)
         ->whereDate('datetime_start', $datetime_start)
         ->whereDate('datetime_end', $datetime_end)
         ->where('state', '!=', TripState::CANCELLATION_REQUEST)
         ->where('state', '!=', TripState::CANCELLED)->get();
          return count($duplicates) > 0;
     }
}
