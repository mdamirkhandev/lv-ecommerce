<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartContent = Cart::content();
        return view('user.cart', compact('cartContent'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if (Cart::count() > 0) {
            $cartContent = Cart::content();
            $productExist = false;
            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productExist = true;
                    break;
                }
            }
            if ($productExist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product already in cart'
                ]);
            } else {
                Cart::add([
                    'id' => $product->id,
                    'name' => $product->title,
                    'qty' => 1,
                    'price' => $product->price,
                    'options' => ['productImage' => $product->product_images ? $product->product_images->first() : '']
                ]);
                $status = true;
                $message = 'Product added in cart';
            }
        } else {
            // Cart::add(['id' => $product->id, 'name' => $product->name, 'qty' => 1, 'price' => $product->price, 'options' => ['size' => 'large']]);
            Cart::add([
                'id' => $product->id,
                'name' => $product->title,
                'qty' => 1,
                'price' => $product->price,
                'options' => ['productImage' => $product->product_images ? $product->product_images->first() : '']
            ]);
            $message = 'Product added to the cart successfully';
            flash($message, 'success');
            $status = true;
            $message = 'Product added in cart';
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;
        //check stock avilability
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);
        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                flash($message, 'success');
                return response()->json(
                    [
                        'status' => true,
                        'message' => $message
                    ]
                );
            } else {
                $message = 'Requested qty:-' . $qty . ' is not available 😐';
                flash($message, 'error');
                return response()->json(
                    [
                        'status' => false,
                        'message' => $message
                    ]
                );
            }
        } else {
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            flash($message, 'success');
            return response()->json(
                [
                    'status' => true,
                    'message' => $message
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $rowId = $request->rowId;
        Cart::remove($rowId);
        $message = 'Product deleted from cart and cart updated successfully';
        flash($message, 'success');
        return response()->json(
            [
                'status' => true,
                'message' => $message
            ]
        );
    }
}
