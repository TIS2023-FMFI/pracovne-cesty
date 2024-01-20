<?php

namespace App\Http\Controllers;


use App\Enums\PritomnostAbsenceType;
use App\Enums\UserType;
use App\Models\BusinessTrip;
use App\Models\PritomnostAbsence;
use App\Models\PritomnostUser;
use App\Models\User;
use DateInterval;
use DateTime;
use DatePeriod;
use Exception;

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
                $newUser = User::create([
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

                //Add type of user
                $newUser->user_type = UserType::EMPLOYEE; // Set the appropriate user type
                $newUser->save();
            }
        }
    }

    /**
     * Synchronize business trips between Cesty and Pritomnost databases.
     *
     * @return void
     * @throws Exception If there is an issue with DateTime
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
            // Calculate the number of days in the business trip
            $startDate = new DateTime($businessTrip->datetime_start);
            $endDate = new DateTime($businessTrip->datetime_end);
            $interval = new DateInterval('P1D');
            $dateRange = new DatePeriod($startDate, $interval, $endDate);

            foreach ($dateRange as $date) {
                // Calculate from_time and to_time for the current day in the loop
                $fromTime = ($date == $startDate) ? $businessTrip->datetime_start : $date->format('Y-m-d') . ' 00:00:00';
                $toTime = ($date == $endDate) ? $businessTrip->datetime_end : $date->format('Y-m-d') . ' 23:59:59';

                // Check if the absence already exists in the Pritomnost database for this day
                $existingAbsence = PritomnostAbsence::where([
                    'user_id' => $businessTrip->user_id,
                    'from_time' => $fromTime,
                    'to_time' => $toTime,
                    'type' => PritomnostAbsenceType::BUSINESS_TRIP,
                    'description' => 'Pracovna cesta z Cesty DB',
                ])->first();

                if (!$existingAbsence) {
                    //Create absence record in the Pritomnost database
                    PritomnostAbsence::create([
                        'user_id' => $businessTrip->user_id,
                        'from_time' => $fromTime,
                        'to_time' => $toTime,
                        'type' => PritomnostAbsenceType::BUSINESS_TRIP,
                        'description' => 'Pracovna cesta z Cesty DB',
                        //Other values are not defined
                    ]);
                }
            }
        }
    }
}
