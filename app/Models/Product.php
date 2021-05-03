<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    /**
     * Get the product variant for the product.
     */
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the variant that owns the ProductVariant.
     */
    public function productPrice()
    {
        return $this->belongsTo(ProductVariantPrice::class,'id','product_id');
    }

    public function productVariantPrice()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }

}
