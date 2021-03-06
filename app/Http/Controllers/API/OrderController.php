<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Http\Requests\CreateOrderRequest;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        if(\Auth::user()->role_id == 2) {
            if(\Auth::id() != $userId) {
                abort(403, 'Access Forbidden');
            }
        }

        return OrderCollection::collection(Order::where('user_id',$userId)->paginate(10));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($productId, CreateOrderRequest $request)
    {
        $product = \App\Product::findOrFail($productId);

        if($request->quantity > $product->stock) {
            \abort(400, 'Your orders more than products stock');
        }
        $order = new Order;
        $order->user_id = \Auth::user()->id;
        $order->quantity = $request->quantity;
        $order->total_price = $request->quantity * $product->price;
        $order->status = "PACKING";
        $product->stock -= $request->quantity;
        $order->save();
        $product->save();

        
        $order->products()->attach($productId);
        

        return response()->json([
            'data' => new OrderResource($order)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($userId, $orderId)
    {
        if(\Auth::user()->role_id == 2) {
            if(\Auth::id() != $userId) {
                abort(403, 'Access Forbidden');
            }
        }

        $order = Order::findOrFail($orderId);

        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request,$userId, $orderId)
    {
        if(\Auth::id() != $userId) {
            abort(403, 'Access Forbidden');
        }

        $order = Order::findOrFail($orderId);

        if($order->status == "SHIPPING") {
            abort(400, 'Product cant\'t be canceled due to shipping');
        } elseif($order->status == "DELIVERED") {
            abort(400, 'Product has been delivered');
        } else {
            $order->status = "CANCELED";
            $order->save();
    
            return new OrderResource($order);
        }
    }

    public function destroy($orderId)
    {
        if(\Auth::user()->role_id == 2) {
            \abort(403, 'Access Forbidden');
        }
        
        $order = Order::findOrFail($orderId);
        $order->delete();

        return response()->json(null, 204);
    }

    public function update(Request $request,$userId, $orderId)
    {
        if(\Auth::user()->role_id == 2) {
            \abort(403, 'Access Forbidden');
        }

        $order = Order::findOrFail($orderId);
        $order->update($request->all());

        return new OrderResource($order);
    }
}
