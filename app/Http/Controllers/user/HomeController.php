<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
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
}
