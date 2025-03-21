<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'notes'
    ];

    /**
     * Les produits associés à ce fournisseur.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('unit_price', 'reference')
            ->withTimestamps();
    }
}
