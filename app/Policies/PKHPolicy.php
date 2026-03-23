<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PKH;
use Illuminate\Auth\Access\HandlesAuthorization;

class PKHPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PKH');
    }

    public function view(AuthUser $authUser, PKH $pKH): bool
    {
        return $authUser->can('View:PKH');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PKH');
    }

    public function update(AuthUser $authUser, PKH $pKH): bool
    {
        return $authUser->can('Update:PKH');
    }

    public function delete(AuthUser $authUser, PKH $pKH): bool
    {
        return $authUser->can('Delete:PKH');
    }

    public function restore(AuthUser $authUser, PKH $pKH): bool
    {
        return $authUser->can('Restore:PKH');
    }

    public function forceDelete(AuthUser $authUser, PKH $pKH): bool
    {
        return $authUser->can('ForceDelete:PKH');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PKH');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PKH');
    }

    public function replicate(AuthUser $authUser, PKH $pKH): bool
    {
        return $authUser->can('Replicate:PKH');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PKH');
    }

}