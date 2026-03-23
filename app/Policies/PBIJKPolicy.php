<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PBIJK;
use Illuminate\Auth\Access\HandlesAuthorization;

class PBIJKPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PBIJK');
    }

    public function view(AuthUser $authUser, PBIJK $pBIJK): bool
    {
        return $authUser->can('View:PBIJK');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PBIJK');
    }

    public function update(AuthUser $authUser, PBIJK $pBIJK): bool
    {
        return $authUser->can('Update:PBIJK');
    }

    public function delete(AuthUser $authUser, PBIJK $pBIJK): bool
    {
        return $authUser->can('Delete:PBIJK');
    }

    public function restore(AuthUser $authUser, PBIJK $pBIJK): bool
    {
        return $authUser->can('Restore:PBIJK');
    }

    public function forceDelete(AuthUser $authUser, PBIJK $pBIJK): bool
    {
        return $authUser->can('ForceDelete:PBIJK');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PBIJK');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PBIJK');
    }

    public function replicate(AuthUser $authUser, PBIJK $pBIJK): bool
    {
        return $authUser->can('Replicate:PBIJK');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PBIJK');
    }

}