<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DTKS;
use Illuminate\Auth\Access\HandlesAuthorization;

class DTKSPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DTKS');
    }

    public function view(AuthUser $authUser, DTKS $dTKS): bool
    {
        return $authUser->can('View:DTKS');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DTKS');
    }

    public function update(AuthUser $authUser, DTKS $dTKS): bool
    {
        return $authUser->can('Update:DTKS');
    }

    public function delete(AuthUser $authUser, DTKS $dTKS): bool
    {
        return $authUser->can('Delete:DTKS');
    }

    public function restore(AuthUser $authUser, DTKS $dTKS): bool
    {
        return $authUser->can('Restore:DTKS');
    }

    public function forceDelete(AuthUser $authUser, DTKS $dTKS): bool
    {
        return $authUser->can('ForceDelete:DTKS');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DTKS');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DTKS');
    }

    public function replicate(AuthUser $authUser, DTKS $dTKS): bool
    {
        return $authUser->can('Replicate:DTKS');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DTKS');
    }

}