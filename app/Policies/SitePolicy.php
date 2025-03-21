<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs peuvent voir la liste des sites
    }

    public function view(User $user, Site $site): bool
    {
        return true; // Tous les utilisateurs peuvent voir les détails d'un site
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Site $site): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Site $site): bool
    {
        return $user->isSuperAdmin();
    }
}
