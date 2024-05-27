<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
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
        return view('user.checkout', compact('countries'));
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
        CustomerAddress::updateOrCreate([
            'user_id' => $user->id,
        ], [
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
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'errrors' => []
        ]);
    }
}
