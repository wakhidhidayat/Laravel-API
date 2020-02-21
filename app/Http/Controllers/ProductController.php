<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    private $statusCode = 500;
    private $status = "error";
    private $message = "";
    private $data = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        $this->middleware('auth:api')->except('index','show');
        
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

        $this->status = "success";
        $this->message = "Add Product Success";
        $this->data = new ProductResource($product);
        $this->statusCode = 201;

        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ], $this->statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
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

        $this->status = "success";
        $this->data = new ProductResource($product);
        $this->message = "Update Product Success";
        $this->statusCode = 200;

        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ], $this->statusCode);
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
        $this->status = "success";
        $this->message = "Delete Product Success";
        $this->statusCode = 204;

        return response()->json([
            "status" => $this->status,
            "message" => $this->message
        ], $this->statusCode);
    }
}
