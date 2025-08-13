<?php

namespace App\Policies;

use App\Models\AssistanceForm;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssistanceFormPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  $user->id || $user->isAdmin() || $user->isManagement();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssistanceForm $assistanceForm): bool
    {
        return $user->id === $assistanceForm->user_id || $user->isAdmin() || $user->isManagement();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->id ;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssistanceForm $assistanceForm): bool
    {
        return $user->id === $assistanceForm->user_id || $user->isAdmin() || $user->isManagement();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssistanceForm $assistanceForm): bool
    {
        return $user->id === $assistanceForm->user_id || $user->isAdmin() || $user->isManagement();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssistanceForm $assistanceForm): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssistanceForm $assistanceForm): bool
    {
        return $user->isAdmin() || $user->isManagement();
    }
}
