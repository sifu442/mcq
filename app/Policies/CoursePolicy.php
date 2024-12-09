<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;
/**
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_course');
    }


    public function view(User $user, Course $course): bool
    {
        return $user->can('view_course');
    }


    public function create(User $user): bool
    {
        return $user->can('create_course');
    }


    public function update(User $user, Course $course): bool
    {
        return $user->can('update_course');
    }


    public function delete(User $user, Course $course): bool
    {
        return $user->can('delete_course');
    }


    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_course');
    }


    public function forceDelete(User $user, Course $course): bool
    {
        return $user->can('force_delete_course');
    }


    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_course');
    }


    public function restore(User $user, Course $course): bool
    {
        return $user->can('restore_course');
    }


    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_course');
    }


    public function replicate(User $user, Course $course): bool
    {
        return $user->can('replicate_course');
    }




    public function reorder(User $user): bool
    {
        return $user->can('reorder_course');
    }
        */
}
