<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Country extends Model
{
    public $timestamps = false;

    /**
     * Get the business trips associated with the country
     *
     * @return HasMany
     */
    public function businessTrips(): HasMany
    {
        return $this->hasMany(BusinessTrip::class, 'country_id');
    }

    public static function getIdOf(string $country): int
    {
        return self::where('name', $country)->first()->id;
    }

    public static function getSortedByTripsCount() : Collection {
        return self::orderby('trips_count', 'desc')->get();
    }

    public static function makeSlovakiaFirst($countries) : Collection {
        $slovakia = collect([$countries->where('name', 'Slovensko')->first()]);
        $otherCountries = $countries->filter(fn($country) => $country->name !== 'Slovensko');
    
        return $slovakia->concat($otherCountries);
    }

    public function incrementTripsCount() : bool {
        return $this->increment('trips_count');
    }

    public function decrementTripsCount() : bool {
        if ($this->trips_count === 0)
            return false;

        return $this->decrement('trips_count');
    }
}
