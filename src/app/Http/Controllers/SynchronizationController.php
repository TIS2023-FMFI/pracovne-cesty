<?php

namespace App\Http\Controllers;

use App\Enums\PritomnostAbsenceType;
use App\Enums\UserType;
use App\Models\BusinessTrip;
use App\Models\PritomnostAbsence;
use App\Models\PritomnostUser;
use App\Models\User;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class SynchronizationController extends Controller
{
    /**
     * Synchronize a single user between Cesty and Pritomnost databases.
     *
     * @param string $username
     * @return bool Whether a user with the given username
     *              has been synchronized from Pritomnost DB or not
     */
    public static function syncSingleUser(string $username): bool
    {
        $cestyDatabase = config('database.connections.cesty.database');
        $dochadzkaDatabase = config('database.connections.dochadzka.database');

        // Use LEFT JOIN to check if the user exists in the Cesty database
        $userToSync = PritomnostUser::leftJoin(
            "{$cestyDatabase}.users",
            "{$cestyDatabase}.users.personal_id", '=', "{$dochadzkaDatabase}.users.personal_id"
        )
            ->where("{$dochadzkaDatabase}.users.username", $username)
            ->where("{$dochadzkaDatabase}.users.personal_id", "<", 10790002)
            ->select("{$dochadzkaDatabase}.users.*", "{$cestyDatabase}.users.id as {$cestyDatabase}_user_id")
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
                $newUser->assignRole('traveller');

                $newUser->save();
            }

            return true;
        }

        return false;
    }


    /**
     * Synchronize a single business trip between Cesty and Pritomnost databases.
     *
     * @param $businessTripId
     * @return bool true if the sync has been successful, false otherwise
     * @throws Exception|Throwable If there is an issue with DateTime or DB connection
     */
    public static function createSingleBusinessTrip($businessTripId): bool
    {
        // Fetch the specific business trip
        $businessTrip = BusinessTrip::find($businessTripId);

        if (!$businessTrip) {
            throw new Exception();
        }

        // Get the Pritomnost user_id
        $pritomnostUser = $businessTrip->user->pritomnostUser()->first();

        if (!$pritomnostUser) {
            throw new Exception();
        }

        $pritomnostUserId = $pritomnostUser->id;

        // Calculate the number of days in the business trip
        $startDate = $businessTrip->datetime_start;
        $endDate = $businessTrip->datetime_end;
        $dateRange = CarbonPeriod::create($startDate->copy()->startOfDay(), '1 day', $endDate->copy()->startOfDay());


        // Start DB transaction
        DB::connection('dochadzka')->beginTransaction();

        try {
            foreach ($dateRange as $date) {
                // Calculate from_time and to_time for the current day in the loop
                $fromTime = $date->isSameDay($startDate) ? $startDate->format('H:i:s') : '00:00:00';
                $toTime = $date->isSameDay($endDate) ? $endDate->format('H:i:s') : '23:59:59';

                // Check if the absence already exists in the Pritomnost database for this day
                $existingAbsence = PritomnostAbsence::where([
                    'user_id' => $pritomnostUserId,
                    'date_time' => $date->format('Y-m-d'),
                ])->exists();

                if (!$existingAbsence) {
                    // Create absence record in the Pritomnost database
                    PritomnostAbsence::create([
                        'user_id' => $pritomnostUserId,
                        'date_time' => $date->format('Y-m-d'),
                        'from_time' => $fromTime,
                        'to_time' => $toTime,
                        'description' => $businessTrip->type->inSlovak() . ' pracovná cesta ' . $businessTrip->place,
                        'type' => PritomnostAbsenceType::BUSINESS_TRIP,
                        'confirmation' => 0,
                        'cesty_id' => $businessTripId
                        // Other values are not defined
                    ]);
                }
            }

            DB::connection('dochadzka')->commit();

        } catch (Exception $e) {
            DB::connection('dochadzka')->rollBack();
            return false;
        }

        return true;
    }

    /**
     * Deletes a cancelled business trip from the Pritomnost database.
     *
     * @param int $businessTripId The ID of the business trip to be deleted.
     * @return bool Returns true if the business trip was successfully deleted, false otherwise.
     */
    public static function deleteCancelledBusinessTrip($businessTripId) : bool {
        $businessTrip = BusinessTrip::find($businessTripId);

        if (!$businessTrip) {
            throw new Exception();
        }

        $pritomnostUser = $businessTrip->user->pritomnostUser()->first();

        if (!$pritomnostUser) {
            throw new Exception();
        }

        DB::connection('dochadzka')->beginTransaction();

        try {
            PritomnostAbsence::where('user_id', $pritomnostUser->id)
                ->whereNotNull('cesty_id')
                ->where('cesty_id', $businessTripId)
                ->delete();

            DB::connection('dochadzka')->commit();

        } catch (Exception $e) {
            DB::connection('dochadzka')->rollBack();
            return false;
        }

        return true;
    }

    /**
     * Updates a single business trip by first deleting it from the Prtiomnost system's database and then creating it again with the updated data.
     *
     * @param int $businessTripId The ID of the business trip to update.
     * @return bool Returns true if the business trip was successfully updated, false otherwise.
     */
    public static function updateSingleBusinessTrip($businessTripId) : bool {
        if (!self::deleteCancelledBusinessTrip($businessTripId)) {
            return false;
        }

        return self::createSingleBusinessTrip($businessTripId);
    }
}
