<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BPNT;
use Illuminate\Auth\Access\HandlesAuthorization;

class BPNTPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BPNT');
    }

    public function view(AuthUser $authUser, BPNT $bPNT): bool
    {
        return $authUser->can('View:BPNT');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BPNT');
    }

    public function update(AuthUser $authUser, BPNT $bPNT): bool
    {
        return $authUser->can('Update:BPNT');
    }

    public function delete(AuthUser $authUser, BPNT $bPNT): bool
    {
        return $authUser->can('Delete:BPNT');
    }

    public function restore(AuthUser $authUser, BPNT $bPNT): bool
    {
        return $authUser->can('Restore:BPNT');
    }

    public function forceDelete(AuthUser $authUser, BPNT $bPNT): bool
    {
        return $authUser->can('ForceDelete:BPNT');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BPNT');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BPNT');
    }

    public function replicate(AuthUser $authUser, BPNT $bPNT): bool
    {
        return $authUser->can('Replicate:BPNT');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BPNT');
    }

}