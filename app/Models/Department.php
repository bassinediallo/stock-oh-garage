<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    protected $fillable = ['name', 'site_id'];

    protected $appends = ['products_count', 'low_stock_count'];

    /**
     * Obtient le site associé au département.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Obtient les mouvements de stock du département.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Obtient les stocks du département.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(DepartmentStock::class);
    }

    /**
     * Calcule le stock actuel d'un produit dans ce département.
     */
    public function getCurrentStock(Product $product): int
    {
        return $this->stocks()
            ->where('product_id', $product->id)
            ->value('quantity') ?? 0;
    }

    /**
     * Obtient le nombre de produits uniques dans ce département.
     */
    public function getProductsCountAttribute(): int
    {
        return $this->stocks()
            ->where('quantity', '>', 0)
            ->count();
    }

    /**
     * Obtient le nombre de produits en stock faible dans ce département.
     */
    public function getLowStockCountAttribute(): int
    {
        return DB::table('department_stocks')
            ->join('products', 'department_stocks.product_id', '=', 'products.id')
            ->where('department_stocks.department_id', $this->id)
            ->whereRaw('department_stocks.quantity <= products.minimum_stock')
            ->where('department_stocks.quantity', '>', 0)
            ->count();
    }

    /**
     * Obtient tous les produits en stock faible dans ce département.
     */
    public function getLowStockProducts()
    {
        return Product::join('department_stocks', 'products.id', '=', 'department_stocks.product_id')
            ->where('department_stocks.department_id', $this->id)
            ->whereRaw('department_stocks.quantity <= products.minimum_stock')
            ->where('department_stocks.quantity', '>', 0)
            ->select('products.*', 'department_stocks.quantity as current_stock')
            ->get();
    }

    /**
     * Obtient tous les produits dans ce département avec leurs quantités.
     */
    public function getProductsWithStock()
    {
        return Product::join('department_stocks', 'products.id', '=', 'department_stocks.product_id')
            ->where('department_stocks.department_id', $this->id)
            ->where('department_stocks.quantity', '>', 0)
            ->select('products.*', 'department_stocks.quantity as current_stock')
            ->orderBy('products.name')
            ->get();
    }
}
