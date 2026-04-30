<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.viewAny');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('users.delete');
    }

    public function changeRole(User $user, User $model): bool
    {
        return $user->can('users.changeRole');
    }

    public function ban(User $user, User $model): bool
    {
        return $user->can('users.ban');
    }

    public function unban(User $user, User $model): bool
    {
        return $user->can('users.unban');
    }

    public function impersonate(User $user, User $model): bool
    {
        if (! $user->can('users.impersonate')) {
            return false;
        }

        return ! $model->hasRole(RoleEnum::SuperAdmin->value);
    }
}
