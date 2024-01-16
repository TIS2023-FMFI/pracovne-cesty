<?php

namespace App\Enums;

enum TripState: int
{
    // Business trip is submitted
    case NEW = 0;

    // Business trip is confirmed by a secretariat
    case CONFIRMED = 1;

    // The business trip happens

    // Upon returning from the trip,
    // the traveller can update trip details

    // Trip details are updated
    case UPDATED = 2;

    // Trip editing is finalized
    case COMPLETED = 3;

    // A secretariat does an accounting for the trip

    // Trip is closed as processed
    case CLOSED = 4;

    // In case of a cancellation prior to the trip happening

    // The traveller has requested a cancellation of the trip
    case CANCELLATION_REQUEST = 5;

    // Trip cancellation is approved by a secretariat
    case CANCELLED = 6;
}
