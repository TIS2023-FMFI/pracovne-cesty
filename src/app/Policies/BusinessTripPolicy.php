<?php

namespace App\Policies;

use App\Models\BusinessTrip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BusinessTripPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BusinessTrip $businessTrip): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('traveller') && $businessTrip->user->is($user));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin|traveller');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BusinessTrip $businessTrip): bool
    {
        return $this->view($user, $businessTrip);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BusinessTrip $businessTrip): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BusinessTrip $businessTrip): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BusinessTrip $businessTrip): bool
    {
        //
    }
}
