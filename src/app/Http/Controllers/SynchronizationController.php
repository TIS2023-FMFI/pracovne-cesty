<?php

namespace App\Http\Controllers;


use App\Enums\PritomnostAbsenceType;
use App\Models\BusinessTrip;
use App\Models\PritomnostAbsence;
use App\Models\PritomnostUser;
use App\Models\User;

class SynchronizationController extends Controller
{
    /**
     * Synchronize users between Cesty and Pritomnost databases.
     *
     * @return void
     */
    public function syncUsers(): void
    {
        // Use LEFT JOIN to check if the user exists in the Cesty database
        $usersToSync = PritomnostUser::leftJoin('cesty.users', 'cesty.users.personal_id', '=', 'pritomnost.users.personal_id')
            ->select('pritomnost.users.*', 'cesty.users.id as cesty_user_id')
            ->get();

        foreach ($usersToSync as $user) {
            //Check if the user exists in the database Cesty
            if ($user->cesty_user_id) {
                //Update user details in the Cesty database based on the Pritomnost database
                User::where('id', $user->cesty_user_id)->update([
                    'personal_id' => $user->personal_id,
                    'username' => $user->username,
                    'password' => $user->password,
                    'first_name' => $user->name,
                    'last_name' => $user->surname,
                    'email' => $user->email,
                    'status' => $user->status,
                    'last_login' => $user->last_login,
                    //Other values are not defined
                ]);
            } else {
                //User doesn't exist in the Cesty database, create them
                User::create([
                    'personal_id' => $user->personal_id,
                    'username' => $user->username,
                    'password' => $user->password,
                    'first_name' => $user->name,
                    'last_name' => $user->surname,
                    'email' => $user->email,
                    'status' => $user->status,
                    'last_login' => $user->last_login,
                    //Other values are not defined
                ]);
            }
        }
    }

    /**
     * Synchronize business trips between Cesty and Pritomnost databases.
     *
     * @return void
     */
    public function syncBusinessTrips(): void
    {
        // Use LEFT JOIN to check if the absence already exists in the Pritomnost database
        $businessTripsToSync = BusinessTrip::leftJoin('pritomnost.absences', function ($join) {
            $join->on('absences.user_id', '=', 'business_trips.user_id')
                ->where('absences.from_time', '=', 'business_trips.datetime_start')
                ->where('absences.to_time', '=', 'business_trips.datetime_end')
                ->where('absences.type', '=', PritomnostAbsenceType::BUSINESS_TRIP);
        })
            ->select('business_trips.*', 'absences.id as absence_id')
            ->get();

        foreach ($businessTripsToSync as $businessTrip) {
            //Check if the absence already exists in the Pritomnost database
            if (!$businessTrip->absence_id) {
                //Create absence record in the Pritomnost database
                PritomnostAbsence::create([
                    'user_id' => $businessTrip->user_id,
                    'from_time' => $businessTrip->datetime_start,
                    'to_time' => $businessTrip->datetime_end,
                    //Other values are not defined
                ]);
            }
        }
    }
}
