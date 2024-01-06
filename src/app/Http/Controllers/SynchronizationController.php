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
        /**
         * Get all users from the database Pritomnost
         */
        $pritomnostUsers = PritomnostUser::all();

        foreach ($pritomnostUsers as $pritomnostUser) {
            /**
             * Check if the user exists in the database Cesty
             */
            $cestyUser = User::where('personal_id', $pritomnostUser->personal_id)->first();

            if ($cestyUser) {
                /**
                 * Update user details in the Cesty database based on the Pritomnost database
                 */
                $cestyUser->update([
                    'personal_id' => $pritomnostUser->personal_id,
                    'username' => $pritomnostUser->username,
                    'password' => $pritomnostUser->password,
                    'first_name' => $pritomnostUser->name,
                    'last_name' => $pritomnostUser->surname,
                    'email' => $pritomnostUser->email,
                    'status' => $pritomnostUser->status,
                    'last_login' => $pritomnostUser->last_login,
                    /**
                     * Other values are not defined
                     */
                ]);
            } else {
                /**
                 * User doesn't exist in the Pritomnost database, create them
                 */
                User::create([
                    'personal_id' => $pritomnostUser->personal_id,
                    'username' => $pritomnostUser->username,
                    'password' => $pritomnostUser->password,
                    'first_name' => $pritomnostUser->name,
                    'last_name' => $pritomnostUser->surname,
                    'email' => $pritomnostUser->email,
                    'status' => $pritomnostUser->status,
                    'last_login' => $pritomnostUser->last_login,
                    /**
                     * Other values are not defined
                     */
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
        /**
         * Get all business trips from the database Cesty
         */
        $businessTrips = BusinessTrip::all();

        foreach ($businessTrips as $businessTrip) {
            /**
             * Check if the absence already exists in the database Pritomnost
             */
            $existingAbsence = PritomnostAbsence::where([
                'user_id' => $businessTrip->user_id,
                'from_time' => $businessTrip->datetime_start,
                'to_time' => $businessTrip->datetime_end,
                'type' => PritomnostAbsenceType::BUSINESS_TRIP,
            ])->first();

            if (!$existingAbsence) {
                /**
                 * Create absence record in the database Pritomnost
                 */
                PritomnostAbsence::create([
                    'user_id' => $businessTrip->user_id,
                    'from_time' => $businessTrip->datetime_start,
                    'to_time' => $businessTrip->datetime_end,
                    /**
                     * Other values are not defined
                     */
                ]);
            }
        }
    }
}
