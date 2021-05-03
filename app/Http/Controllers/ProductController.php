<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\QueryFilters\ProductFilter;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request, ProductFilter $filter)
    {
        $products = Product::with(
            'productVariants',
            'productVariants.productVariantPrice',
            'productVariants.variantDetails',
            'productVariantPrice'
        )->filter($filter)->paginate(5);

        $variants = Variant::with('productVariants')->get();

        return view('products.index', compact('products', 'variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sku' => 'required|string|unique:products',
            'description' => 'required|string',
            'product_variant' => 'required|array',
            'product_variant_prices' => 'required|array',
        ]);


        try {

            \DB::transaction(function () use ($request) {
                $product = Product::create([
                    'title' => $request->title,
                    'sku' => $request->sku,
                    'description' => $request->description,
                ]);

                $productVariants = $request->product_variant;
                $productVariantData = [];

                foreach ($productVariants as $productVariant) {

                    foreach ($productVariant['tags'] as $key => $tag) {

                        $productVariantData[] = [
                            'variant' => $tag,
                            'variant_id' => $productVariant['option'],
                            'product_id' => $product->id,
                        ];
                    }

                }
                ProductVariant::insert($productVariantData);

                //productVariantPrices
                $productVariantPrices = $request->product_variant_prices;
                $productVariantPricesData = [];

                foreach ($productVariantPrices as $productVariantPrice) {

                    $array = explode('/', $productVariantPrice['title']); // 0 = variant ,1 =variant , 2 = /
                    $productVariant = ProductVariant::wherevariant($array[1])->first();

                    $productVariantPricesData[] = [

                        'product_variant_one' => $productVariant->variant_id,
                        'price' => number_format($productVariant->price, 2),
                        'stock' => number_format($productVariant->stock),
                        'product_id' => $product->id,

                    ];

                }
                ProductVariantPrice::insert($productVariantPricesData);
            });

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['msg' => 'something went wrong.'], 200);
        }

        return response()->json(['msg' => 'successfully created'], 200);


    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with(
            'productVariants',
            'productVariants.variantDetails'
        )->findOrFail($id);

        $variants = Variant::all();

        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $request->validate([
            'title' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'description' => 'required|string',
        ]);

        try {
            Product::findOrFail($id)->update($request->all());

        } catch (\Exception $e) {

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['msg' => 'something went wrong.'], 200);
        }

        return response()->json(['msg' => 'successfully updated'], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
