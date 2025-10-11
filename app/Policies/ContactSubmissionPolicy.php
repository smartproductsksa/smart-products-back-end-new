<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ContactSubmission;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactSubmissionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContactSubmission');
    }

    public function view(AuthUser $authUser, ContactSubmission $contactSubmission): bool
    {
        return $authUser->can('View:ContactSubmission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContactSubmission');
    }

    public function update(AuthUser $authUser, ContactSubmission $contactSubmission): bool
    {
        return $authUser->can('Update:ContactSubmission');
    }

    public function delete(AuthUser $authUser, ContactSubmission $contactSubmission): bool
    {
        return $authUser->can('Delete:ContactSubmission');
    }

    public function restore(AuthUser $authUser, ContactSubmission $contactSubmission): bool
    {
        return $authUser->can('Restore:ContactSubmission');
    }

    public function forceDelete(AuthUser $authUser, ContactSubmission $contactSubmission): bool
    {
        return $authUser->can('ForceDelete:ContactSubmission');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContactSubmission');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContactSubmission');
    }

    public function replicate(AuthUser $authUser, ContactSubmission $contactSubmission): bool
    {
        return $authUser->can('Replicate:ContactSubmission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContactSubmission');
    }

}