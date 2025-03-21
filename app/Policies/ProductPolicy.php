<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs peuvent voir la liste des produits
    }

    public function view(User $user, Product $product): bool
    {
        return true; // Tous les utilisateurs peuvent voir les détails d'un produit
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isStockManager();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isSuperAdmin() || $user->isStockManager();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isSuperAdmin();
    }
}
