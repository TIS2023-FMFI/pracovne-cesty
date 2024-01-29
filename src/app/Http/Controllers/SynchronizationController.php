<?php

namespace App\Http\Controllers;


use App\Enums\PritomnostAbsenceType;
use App\Enums\UserType;
use App\Models\BusinessTrip;
use App\Models\PritomnostAbsence;
use App\Models\PritomnostUser;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

class SynchronizationController extends Controller
{
    /**
     * Synchronize a single user between Cesty and Pritomnost databases.
     *
     * @param $userId
     * @return void
     */
    public static function syncSingleUser($userId): void
    {
        // Use LEFT JOIN to check if the user exists in the Cesty database
        $userToSync = PritomnostUser::leftJoin(
            'cesty.users',
            'cesty.users.personal_id', '=', 'pritomnost.users.personal_id'
        )
            ->where('pritomnost.users.id', $userId)
            ->select('pritomnost.users.*', 'cesty.users.id as cesty_user_id')
            ->first();

        // Update user details in the Cesty database based on the Pritomnost database
        if ($userToSync) {
            if ($userToSync->cesty_user_id) {
                User::where('id', $userToSync->cesty_user_id)->update([
                    'personal_id' => $userToSync->personal_id,
                    'username' => $userToSync->username,
                    'password' => $userToSync->password,
                    'first_name' => $userToSync->name,
                    'last_name' => $userToSync->surname,
                    'email' => $userToSync->email,
                    'status' => $userToSync->status,
                    'last_login' => $userToSync->last_login,
                    // Other values are not defined
                ]);
            } else {
                // User doesn't exist in the Cesty database, create them
                $newUser = User::create([
                    'personal_id' => $userToSync->personal_id,
                    'username' => $userToSync->username,
                    'password' => $userToSync->password,
                    'first_name' => $userToSync->name,
                    'last_name' => $userToSync->surname,
                    'email' => $userToSync->email,
                    'status' => $userToSync->status,
                    'last_login' => $userToSync->last_login,
                    // Other values are not defined
                ]);

                // Add type of user
                $newUser->user_type = UserType::EMPLOYEE;
                $newUser->save();
            }
        }
    }


    /**
     * Synchronize a single business trip between Cesty and Pritomnost databases.
     *
     * @param $businessTripId
     * @return void
     * @throws Exception If there is an issue with DateTime
     */
    public static function syncSingleBusinessTrip($businessTripId): void
    {
        // Fetch the specific business trip
        $businessTrip = BusinessTrip::findOrFail($businessTripId);

        // Calculate the number of days in the business trip
        $startDate = new DateTime($businessTrip->datetime_start);
        $endDate = new DateTime($businessTrip->datetime_end);
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate);

        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');

            // Calculate from_time and to_time for the current day in the loop
            $fromTime = ($date == $startDate) ? $startDate->format('H:i:s') : '00:00:00';
            $toTime = ($date == $endDate) ? $endDate->format('H:i:s') : '23:59:59';

            // Get the Pritomnost user_id
            $pritomnostUserId = $businessTrip->user()
                ->first()
                ->pritomnostUser()
                ->first()
                ->id;

            // Check if the absence already exists in the Pritomnost database for this day
            $existingAbsence = PritomnostAbsence::where([
                'user_id' => $pritomnostUserId,
                'date_time' => $formattedDate,
                'type' => PritomnostAbsenceType::BUSINESS_TRIP,
            ])->first();

            if (!$existingAbsence) {
                // Create absence record in the Pritomnost database
                PritomnostAbsence::create([
                    'user_id' => $pritomnostUserId,
                    'date_time' => $formattedDate,
                    'from_time' => $fromTime,
                    'to_time' => $toTime,
                    'type' => PritomnostAbsenceType::BUSINESS_TRIP,
                    'description' => 'Pracovná cesta zo systému Cesty',
                    // Other values are not defined
                ]);
            }
        }
    }
}