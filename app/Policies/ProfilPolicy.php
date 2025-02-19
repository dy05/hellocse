<?php

namespace App\Policies;

use App\Models\Administrator;
use App\Models\Profil;
use Illuminate\Auth\Access\Response;

class ProfilPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Administrator $administrator): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Administrator $administrator, Profil $profil): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Administrator $administrator): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Administrator $administrator, Profil $profil): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Administrator $administrator, Profil $profil): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Administrator $administrator, Profil $profil): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Administrator $administrator, Profil $profil): bool
    {
        return false;
    }
}
