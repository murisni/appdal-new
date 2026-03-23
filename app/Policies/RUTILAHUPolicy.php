<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RUTILAHU;
use Illuminate\Auth\Access\HandlesAuthorization;

class RUTILAHUPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RUTILAHU');
    }

    public function view(AuthUser $authUser, RUTILAHU $rUTILAHU): bool
    {
        return $authUser->can('View:RUTILAHU');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RUTILAHU');
    }

    public function update(AuthUser $authUser, RUTILAHU $rUTILAHU): bool
    {
        return $authUser->can('Update:RUTILAHU');
    }

    public function delete(AuthUser $authUser, RUTILAHU $rUTILAHU): bool
    {
        return $authUser->can('Delete:RUTILAHU');
    }

    public function restore(AuthUser $authUser, RUTILAHU $rUTILAHU): bool
    {
        return $authUser->can('Restore:RUTILAHU');
    }

    public function forceDelete(AuthUser $authUser, RUTILAHU $rUTILAHU): bool
    {
        return $authUser->can('ForceDelete:RUTILAHU');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RUTILAHU');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RUTILAHU');
    }

    public function replicate(AuthUser $authUser, RUTILAHU $rUTILAHU): bool
    {
        return $authUser->can('Replicate:RUTILAHU');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RUTILAHU');
    }

}