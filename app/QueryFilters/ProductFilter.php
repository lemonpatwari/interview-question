<?php


namespace App\QueryFilters;


use App\Enums\TenantStatus;
use Carbon\Carbon;
use Faisal50x\QueryFilter\QueryFilter;

class ProductFilter extends QueryFilter
{
    /**
     * @param $query
     * @param string|null $q
     * @return mixed
     */
    public function title($query, string $q = null)
    {
        $q = str_replace(" ", '', trim($q));
        if (empty($q)) {
            return $query;
        }
        return $query->where(function ($query) use ($q) {
            return $query->where('title', "LIKE", "%$q%");
        });
    }

    /**
     * @param $query
     * @param string|null $q
     * @return mixed
     */
    public function date($query, string $q = null)
    {
        if (empty($q)) {
            return $query;
        }
        $formatDate = Carbon::parse($q)->format('Y-m-d');

        return $query->where(function ($query) use ($formatDate) {
            return $query->whereDate('created_at', $formatDate);
        });
    }


    public function variantId($query, int $variantId = null)
    {
        if (is_null($variantId)) {
            return $query;
        }
        return $query->whereHas('productVariants', function ($q) use ($variantId) {
            $q->wherevariantId($variantId);
        });
    }

    public function price($query, $price = null)
    {

        if (empty($price)) {
            return $query;
        }

        $priceArray = explode('-', $price); // 0 = from ; 1 = to
        return $query->whereHas('productVariantPrice', function ($q) use ($priceArray) {
            $q->where('price','>=',$priceArray[0])->where('price','<=',$priceArray[1]);
        });

    }


}
