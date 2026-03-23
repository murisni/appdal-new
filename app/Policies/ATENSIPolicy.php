<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ATENSI;
use Illuminate\Auth\Access\HandlesAuthorization;

class ATENSIPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ATENSI');
    }

    public function view(AuthUser $authUser, ATENSI $aTENSI): bool
    {
        return $authUser->can('View:ATENSI');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ATENSI');
    }

    public function update(AuthUser $authUser, ATENSI $aTENSI): bool
    {
        return $authUser->can('Update:ATENSI');
    }

    public function delete(AuthUser $authUser, ATENSI $aTENSI): bool
    {
        return $authUser->can('Delete:ATENSI');
    }

    public function restore(AuthUser $authUser, ATENSI $aTENSI): bool
    {
        return $authUser->can('Restore:ATENSI');
    }

    public function forceDelete(AuthUser $authUser, ATENSI $aTENSI): bool
    {
        return $authUser->can('ForceDelete:ATENSI');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ATENSI');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ATENSI');
    }

    public function replicate(AuthUser $authUser, ATENSI $aTENSI): bool
    {
        return $authUser->can('Replicate:ATENSI');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ATENSI');
    }

}