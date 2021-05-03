<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /**
     * Get the variant that owns the ProductVariant.
     */
    public function variantDetails()
    {
        return $this->belongsTo(Variant::class,'variant_id','id');
    }


    public function productVariantPrice()
    {
        return $this->belongsTo(ProductVariantPrice::class,'id','product_variant_one');
    }
}
