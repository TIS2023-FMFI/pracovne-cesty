<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripContribution extends Model
{
    use HasFactory;

    protected $fillable = ['business_trip_id', 'contribution_id', 'detail'];
}
