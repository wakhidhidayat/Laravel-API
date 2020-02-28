<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        
        $this->middleware(function($request, $next) {
            if(Gate::allows('manage-products')) return $next($request);
            abort(403, 'Access Forbidden');
        })->except('index','show');
    }

    public function index()
    {
        return ProductCollection::collection(Product::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->stock = $request->stock;
        $product->price = $request->price;
        $product->detail = $request->detail;
        $product->discount = $request->discount;
        $product->save();

        return response()->json([
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($productId)
    {
        $product = Product::findOrFail($productId);
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request['detail'] = $request->description;
        unset($request['description']);
        $product->update($request->all());

        return response()->json([
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
