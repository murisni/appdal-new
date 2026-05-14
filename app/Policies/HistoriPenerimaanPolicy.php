<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HistoriPenerimaan;
use Illuminate\Auth\Access\HandlesAuthorization;

class HistoriPenerimaanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HistoriPenerimaan');
    }

    public function view(AuthUser $authUser, HistoriPenerimaan $historiPenerimaan): bool
    {
        return $authUser->can('View:HistoriPenerimaan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HistoriPenerimaan');
    }

    public function update(AuthUser $authUser, HistoriPenerimaan $historiPenerimaan): bool
    {
        return $authUser->can('Update:HistoriPenerimaan');
    }

    public function delete(AuthUser $authUser, HistoriPenerimaan $historiPenerimaan): bool
    {
        return $authUser->can('Delete:HistoriPenerimaan');
    }

    public function restore(AuthUser $authUser, HistoriPenerimaan $historiPenerimaan): bool
    {
        return $authUser->can('Restore:HistoriPenerimaan');
    }

    public function forceDelete(AuthUser $authUser, HistoriPenerimaan $historiPenerimaan): bool
    {
        return $authUser->can('ForceDelete:HistoriPenerimaan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HistoriPenerimaan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HistoriPenerimaan');
    }

    public function replicate(AuthUser $authUser, HistoriPenerimaan $historiPenerimaan): bool
    {
        return $authUser->can('Replicate:HistoriPenerimaan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HistoriPenerimaan');
    }

}