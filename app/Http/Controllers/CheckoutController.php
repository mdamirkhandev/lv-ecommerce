<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        if (Cart::count() == 0) {
            return redirect()->route('cart.index');
        }
        if (Auth::check() == false) {
            // if (!session()->has('url.intended')) {
            //     session(['url.intended' => url()->current()]);
            // }
            return redirect()->route('login');
        }
        session()->forget('url.intended');
        $countries = Country::orderBy('name', 'asc')->get();
        $customerDetails = CustomerAddress::where('user_id', Auth::user()->id)->first();
        return view('user.checkout', compact('countries', 'customerDetails'));
    }
    public function orderSubmit(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|min:10',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'All fields are required',
                'errors' => $validation->errors()
            ]);
        }

        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country_id' => $request->country,
            ]
        );
        if ($request->payment_method == 'cod') {
            $shippingCharge = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2, '.', '');
            $grandTotal = $subTotal + $shippingCharge - $discount;

            $order = new Order;
            $order->user_id = $user->id;
            $order->shipping = $shippingCharge;
            $order->subtotal = $subTotal;
            $order->grand_total = $grandTotal;
            $order->payment_method = $request->payment_method;

            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->country_id = $request->country;

            $order->save();

            // Save Order Details
            $products = Cart::content();
            foreach ($products as $product) {
                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->name = $product->name;
                $orderItem->price = $product->price;
                $orderItem->qty = $product->qty;
                $orderItem->total = $product->price * $product->qty;
                $orderItem->save();
            }
            Cart::destroy();
            session()->flash('success', 'Order placed successfully');

            return response()->json([
                'status' => true,
                'orderID' => $order->id,
                'message' => 'Order placed successfully',
            ]);
        } else {
            // 
        }
        // return response()->json([
        //     'status' => true,
        //     'message' => 'Order placed successfully',
        //     'errrors' => []
        // ]);
    }

    public function thankyou($id)
    {
        return view('user.thankyou', compact('id'));
    }
}
