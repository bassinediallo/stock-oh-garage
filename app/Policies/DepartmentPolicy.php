<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs peuvent voir la liste des départements
    }

    public function view(User $user, Department $department): bool
    {
        return true; // Tous les utilisateurs peuvent voir les détails d'un département
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Department $department): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->isSuperAdmin();
    }
}
