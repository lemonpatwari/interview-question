<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public function getFilePathAttribute($value)
    {
        return url('/storage') . '/' . $value;
    }
}
