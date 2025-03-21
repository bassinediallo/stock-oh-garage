<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'reference',
        'description',
        'unit',
        'minimum_stock',
        'status',
        'image',
        'price',
        'category',
        'supplier',
        'location',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            $totalStock = $product->getTotalStock();
            $product->status = $totalStock <= $product->minimum_stock ? 'Stock faible' : 'Stock normal';
        });
    }

    /**
     * Les stocks de ce produit par département.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(DepartmentStock::class);
    }

    /**
     * Les mouvements de stock de ce produit.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Les fournisseurs de ce produit.
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot('unit_price', 'reference')
            ->withTimestamps();
    }

    /**
     * Calcule le stock total du produit tous départements confondus.
     */
    public function getTotalStock(): int
    {
        return $this->stocks()->sum('quantity');
    }

    /**
     * Calcule le stock total du produit pour un département donné.
     */
    public function getCurrentStockForDepartment($departmentId): int
    {
        return $this->stocks()
            ->where('department_id', $departmentId)
            ->value('quantity') ?? 0;
    }

    /**
     * Obtient le dernier mouvement de stock pour un département donné.
     */
    public function getLastMovementForDepartment($departmentId)
    {
        return $this->stockMovements()
            ->where('department_id', $departmentId)
            ->latest()
            ->first();
    }

    /**
     * Met à jour le statut du produit en fonction du stock total et du stock minimum.
     */
    public function updateTotalStock()
    {
        $totalStock = $this->getTotalStock();
        $this->status = $totalStock <= $this->minimum_stock ? 'Stock faible' : 'Stock normal';
        $this->save();
    }

    /**
     * Vérifie si le produit a un stock bas.
     */
    public function hasLowStock()
    {
        return $this->status === 'Stock faible';
    }

    /**
     * Obtient les départements avec un stock bas pour ce produit.
     */
    public function getLowStockDepartments()
    {
        return $this->stocks()
            ->with('department.site')
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($stock) {
                return [
                    'department' => $stock->department,
                    'current_stock' => $stock->quantity
                ];
            });
    }
}
