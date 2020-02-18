<?php

namespace App\Http\Controllers;

use App\Review;
use App\Product;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    private $statusCode = 500;
    private $status = "error";
    private $message = "";
    private $data = null;

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
        $review = new Review;
        $review->review = $request->review;
        $review->star = $request->star;
        $review->user_id = Auth::id();
        $review->product_id = $productId;
        $review->save();

        $this->status = "success";
        $this->message = "Add Review Success";
        $this->statusCode = 201;
        $this->data = new ReviewResource($review);


        return response([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ], $this->statusCode);
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
    
            $this->status = "success";
            $this->message = "Update Review Success";
            $this->statusCode = 200;
            $this->data = new ReviewResource($review);
    
            return response([
                'status' => $this->status,
                'message' => $this->message,
                'data' => $this->data
            ], $this->statusCode);
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
