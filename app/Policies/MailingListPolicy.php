<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MailingList;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailingListPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MailingList');
    }

    public function view(AuthUser $authUser, MailingList $mailingList): bool
    {
        return $authUser->can('View:MailingList');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MailingList');
    }

    public function update(AuthUser $authUser, MailingList $mailingList): bool
    {
        return $authUser->can('Update:MailingList');
    }

    public function delete(AuthUser $authUser, MailingList $mailingList): bool
    {
        return $authUser->can('Delete:MailingList');
    }

    public function restore(AuthUser $authUser, MailingList $mailingList): bool
    {
        return $authUser->can('Restore:MailingList');
    }

    public function forceDelete(AuthUser $authUser, MailingList $mailingList): bool
    {
        return $authUser->can('ForceDelete:MailingList');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MailingList');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MailingList');
    }

    public function replicate(AuthUser $authUser, MailingList $mailingList): bool
    {
        return $authUser->can('Replicate:MailingList');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MailingList');
    }

}