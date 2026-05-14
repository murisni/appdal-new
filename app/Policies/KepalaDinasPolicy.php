<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KepalaDinas;
use Illuminate\Auth\Access\HandlesAuthorization;

class KepalaDinasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KepalaDinas');
    }

    public function view(AuthUser $authUser, KepalaDinas $kepalaDinas): bool
    {
        return $authUser->can('View:KepalaDinas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KepalaDinas');
    }

    public function update(AuthUser $authUser, KepalaDinas $kepalaDinas): bool
    {
        return $authUser->can('Update:KepalaDinas');
    }

    public function delete(AuthUser $authUser, KepalaDinas $kepalaDinas): bool
    {
        return $authUser->can('Delete:KepalaDinas');
    }

    public function restore(AuthUser $authUser, KepalaDinas $kepalaDinas): bool
    {
        return $authUser->can('Restore:KepalaDinas');
    }

    public function forceDelete(AuthUser $authUser, KepalaDinas $kepalaDinas): bool
    {
        return $authUser->can('ForceDelete:KepalaDinas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KepalaDinas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KepalaDinas');
    }

    public function replicate(AuthUser $authUser, KepalaDinas $kepalaDinas): bool
    {
        return $authUser->can('Replicate:KepalaDinas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KepalaDinas');
    }

}