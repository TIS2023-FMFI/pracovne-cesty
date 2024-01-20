<?php

namespace App\Models;

use App\Enums\PritomnostAbsenceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PritomnostAbsence extends Model
{
    protected $connection = 'pritomnost';
    protected $table = 'absence';

    public $timestamps = false;

    protected $casts = [
        'date_time' => 'date',
        'type' => PritomnostAbsenceType::class,
        'insert_time' => 'datetime',
        'public' => 'boolean',
        'confirmation' => 'boolean'
    ];

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'date_time',
        'from_time',
        'to_time',
        'description',
        'type'
    ];

    /**
     * Get the user associated with the absence
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(PritomnostUser::class, 'user_id');
    }
}
