<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Review;
use App\Product;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->except('index','show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $reviews =  Review::all()->where('product_id',$id);
        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReviewRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $ordered = $product->orders->where('user_id', \Auth::id())->first();

        if(!$ordered) {
            \abort(400, 'You haven\'t order this product');
        }

        $review = new Review;
        $review->review = $request->review;
        $review->star = $request->star;
        $review->user_id = Auth::id();
        $review->product_id = $productId;
        $review->save();

        return response([
            'data' => new ReviewResource($review)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show($prodId, Review $review)
    {
        return new ReviewResource($review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(ReviewRequest $request,$prodId, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if(Gate::allows('updateDelete-reviews',$review)) {
            
            $review->update($request->all());
    
            return response([
                'data' => new ReviewResource($review)
            ], 200);
        }
        abort(403, 'Access Forbidden');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy($prodId, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if(Gate::allows('updateDelete-reviews',$review)) {
            $review->delete();
            $this->statusCode = 204;
    
            return response(null, $this->statusCode);
        }
        abort(403, 'Access Forbidden');

    }
}
