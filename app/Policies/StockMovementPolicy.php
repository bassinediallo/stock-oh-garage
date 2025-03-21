<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs peuvent voir la liste des mouvements
    }

    public function view(User $user, StockMovement $movement): bool
    {
        return true; // Tous les utilisateurs peuvent voir les détails d'un mouvement
    }

    public function create(User $user): bool
    {
        // Seuls les super admins et les gestionnaires de stock peuvent créer des mouvements
        if ($user->isConsultant()) {
            return false;
        }

        // Pour les gestionnaires de stock, vérifier qu'ils sont assignés au site
        if ($user->isStockManager()) {
            return true; // La vérification du site sera faite au niveau du contrôleur
        }

        return $user->isSuperAdmin();
    }

    public function viewReport(User $user): bool
    {
        return true; // Tous les utilisateurs peuvent voir les rapports
    }

    public function export(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isStockManager();
    }
}
