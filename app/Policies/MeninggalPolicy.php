<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Meninggal;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeninggalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Meninggal');
    }

    public function view(AuthUser $authUser, Meninggal $meninggal): bool
    {
        return $authUser->can('View:Meninggal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Meninggal');
    }

    public function update(AuthUser $authUser, Meninggal $meninggal): bool
    {
        return $authUser->can('Update:Meninggal');
    }

    public function delete(AuthUser $authUser, Meninggal $meninggal): bool
    {
        return $authUser->can('Delete:Meninggal');
    }

    public function restore(AuthUser $authUser, Meninggal $meninggal): bool
    {
        return $authUser->can('Restore:Meninggal');
    }

    public function forceDelete(AuthUser $authUser, Meninggal $meninggal): bool
    {
        return $authUser->can('ForceDelete:Meninggal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Meninggal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Meninggal');
    }

    public function replicate(AuthUser $authUser, Meninggal $meninggal): bool
    {
        return $authUser->can('Replicate:Meninggal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Meninggal');
    }

}