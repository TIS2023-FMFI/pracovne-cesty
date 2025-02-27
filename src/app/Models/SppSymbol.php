<?php

namespace App\Models;

use App\Enums\SppStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SppSymbol extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => SppStatus::class
    ];

    protected $fillable = [
        'spp_symbol',  'functional_region', 'financial_centre',
        'account', 'grantee', 'status', 'agency', 'acronym'
    ];

    /**
     * Get the business trips associated with the SPP symbol
     *
     * @return HasMany
     */
    public function businessTrips(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'spp_symbol_id');
    }

    /**
     * Get the reimbursements associated with the SPP symbol
     *
     * @return HasMany
     */
    public function reimbursements(): HasMany
    {
        return $this->hasMany(Reimbursement::class, 'spp_symbol_id');
    }

    public function granteeUser()
    {
        return $this->belongsTo(User::class, 'grantee');
    }

}
