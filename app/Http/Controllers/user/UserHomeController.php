<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    //
    public function index()
    {
        $featured_Products = Product::where('is_featured', 'Yes')
            ->orderBy('id', 'DESC')
            ->where('status', 1)
            ->take(8)
            ->get();

        $latest_Products = Product::orderBy('id', 'DESC')
            ->where('status', 1)
            ->take(8)
            ->get();

        return view('user.index', compact('featured_Products', 'latest_Products'));
    }

    public function myAccount()
    {
        return view('user.my-account');
    }

    public function myOrder()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->orderBy('id', 'DESC')->get();
        return view('user.my-orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $user = auth()->user();
        $order = $orders = Order::where('user_id', $user->id)->where('id', $id)->first();
        $orderItems = OrderItem::where('order_id', $id)->get();
        return view('user.order-details', compact('order', 'orderItems'));
    }

    public function logout()
    {
        auth()->logout();
        flash('Logout successfully', 'success');
        return redirect()->route('login');
    }
}
